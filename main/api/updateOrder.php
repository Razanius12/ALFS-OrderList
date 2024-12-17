<?php
// Required headers
header('Content-Type: application/json');

// Include database connection
require_once '../../config/database.php';

try {
 // Start a transaction for atomic operations
 mysqli_begin_transaction($conn);

 // Ensure all required fields are present
 $requiredFields = ['order_id', 'order_name', 'project_manager_id', 'start_date', 'status',];
 foreach ($requiredFields as $field) {
  if (!isset($_POST[$field]) || empty($_POST[$field])) {
   throw new Exception("Missing required field: $field");
  }
 }

 // Sanitize inputs
 $order_id = mysqli_real_escape_string($conn, $_POST['order_id']);
 $order_name = mysqli_real_escape_string($conn, $_POST['order_name']);
 $project_manager_id = mysqli_real_escape_string($conn, $_POST['project_manager_id']);
 $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
 $order_status = mysqli_real_escape_string($conn, $_POST['status']);

 // Optional fields
 $description = isset($_POST['description']) ? mysqli_real_escape_string($conn, $_POST['description']) : '';
 $order_price = isset($_POST['order_price']) ? mysqli_real_escape_string($conn, $_POST['order_price']) : '';
 $assigned_worker = isset($_POST['assigned_worker']) && !empty($_POST['assigned_worker'])
  ? mysqli_real_escape_string($conn, $_POST['assigned_worker'])
  : 'NULL';

 // Check current order status
 $currentStatusQuery = "SELECT status FROM orders WHERE id_order = '$order_id'";
 $result = mysqli_query($conn, $currentStatusQuery);
 if (!$result) {
  throw new Exception('Failed to fetch current order status: ' . mysqli_error($conn));
 }

 $currentStatus = mysqli_fetch_assoc($result)['status'];

 // Check if trying to set status to COMPLETED or CANCELLED while currently PENDING
 if ($currentStatus === 'PENDING' && $order_status === 'COMPLETED') {
  throw new Exception('Cannot set order status to COMPLETED while it is currently PENDING. Please set it to IN_PROGRESS first.');
 }
 if ($currentStatus === 'PENDING' && $order_status === 'CANCELLED') {
  throw new Exception('Cannot set order status to CANCELLED while it is currently PENDING. Please set it to IN_PROGRESS first or you can delete the order directly.');
 }

 // Prepare UPDATE query for orders
 $orderQuery = "UPDATE orders 
                SET 
                 order_name = '$order_name', 
                 project_manager_id = '$project_manager_id', 
                 start_date = '$start_date', 
                 status = '$order_status'";

 // Add description if provided
 if (!empty($description)) {
  $orderQuery .= ", description = '$description'";
 }

 // Add order price
 if (!empty($order_price)) {
  $orderQuery .= ", order_price = '$order_price'";
 }

 // Add worker assignment if provided
 if ($assigned_worker !== 'NULL') {
  $orderQuery .= ", worker_id = '$assigned_worker'";
 } else {
  $orderQuery .= ", worker_id = NULL";
 }

 $orderQuery .= " WHERE id_order = '$order_id'";


 // Execute order update
 if (!mysqli_query($conn, $orderQuery)) {
  throw new Exception('Failed to update order: ' . mysqli_error($conn));
 }

 // If order status is IN_PROGRESS and a worker is assigned
 if ($order_status === 'IN_PROGRESS' && $assigned_worker !== 'NULL') {
  // First, reset any previous worker's assignment
  $resetPreviousWorkerQuery = "UPDATE workers 
                               SET 
                                availability_status = 'AVAILABLE', 
                                assigned_order_id = NULL 
                               WHERE assigned_order_id = '$order_id'";
  if (!mysqli_query($conn, $resetPreviousWorkerQuery)) {
   throw new Exception('Failed to reset previous worker: ' . mysqli_error($conn));
  }

  // Update assigned worker's status and assigned order
  $workerUpdateQuery = "UPDATE workers 
                       SET 
                        availability_status = 'TASKED', 
                        assigned_order_id = '$order_id' 
                       WHERE id_worker = '$assigned_worker'";
  if (!mysqli_query($conn, $workerUpdateQuery)) {
   throw new Exception('Failed to update worker status: ' . mysqli_error($conn));
  }
 } else if ($order_status === 'CANCELLED' or $order_status === 'COMPLETED') {
  // Reset any previous worker's assignment and unlink worker from order in one query
  $query = "UPDATE workers 
            LEFT JOIN orders ON workers.assigned_order_id = orders.id_order
            SET 
              workers.availability_status = 'AVAILABLE', 
              workers.assigned_order_id = NULL,
              orders.worker_id = NULL
            WHERE workers.assigned_order_id = '$order_id'";

  if (!mysqli_query($conn, $query)) {
   throw new Exception('Failed to update worker status and unlink from order: ' . mysqli_error($conn));
  }
 } else if ($assigned_worker === NULL) {
  // If no worker is assigned, reset any previous worker's assignment
  $resetPreviousWorkerQuery = "UPDATE workers 
                               SET 
                                availability_status = 'AVAILABLE', 
                                assigned_order_id = NULL 
                               WHERE assigned_order_id = '$order_id'";
  if (!mysqli_query($conn, $resetPreviousWorkerQuery)) {
   throw new Exception('Failed to reset previous worker: ' . mysqli_error($conn));
  }
 }

 // Commit the transaction
 mysqli_commit($conn);

 echo json_encode([
  'success' => true,
  'message' => 'Order and worker updated successfully',
  'order_id' => $order_id
 ]);

} catch (Exception $e) {
 // Rollback the transaction in case of error
 mysqli_rollback($conn);

 // Error handling
 http_response_code(400); // Bad Request
 echo json_encode([
  'success' => false,
  'message' => $e->getMessage()
 ]);
}

// Close connection
mysqli_close($conn);
?>