<?php
// Required headers
header('Content-Type: application/json');

// Include database connection
require_once '../../config/database.php';

try {
 // Start a transaction for atomic operations
 mysqli_begin_transaction($conn);

 // Ensure all required fields are present
 $requiredFields = ['order_id', 'order_name', 'project_manager_id', 'start_date', 'status', 'deadline'];
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
 $deadline = mysqli_real_escape_string($conn, $_POST['deadline']);

 // Optional fields
 $description = isset($_POST['description']) ? mysqli_real_escape_string($conn, $_POST['description']) : '';
 $order_price = isset($_POST['order_price']) ? mysqli_real_escape_string($conn, $_POST['order_price']) : '';
 $assigned_worker = isset($_POST['assigned_worker']) && !empty($_POST['assigned_worker'])
  ? mysqli_real_escape_string($conn, $_POST['assigned_worker'])
  : 'NULL';

 // Prepare statement to fetch current order status
 $currentStatusStmt = mysqli_prepare($conn, "SELECT status FROM orders WHERE id_order = ?");
 mysqli_stmt_bind_param($currentStatusStmt, "i", $order_id);
 mysqli_stmt_execute($currentStatusStmt);
 $result = mysqli_stmt_get_result($currentStatusStmt);

 if (!$result) {
  throw new Exception('Failed to fetch current order status: ' . mysqli_error($conn));
 }

 $currentStatusRow = mysqli_fetch_assoc($result);
 $currentStatus = $currentStatusRow['status'];

 // Validate status transitions
 $validStatusTransitions = [
  'PENDING' => ['PENDING', 'IN_PROGRESS', 'CANCELLED'],
  'IN_PROGRESS' => ['IN_PROGRESS', 'COMPLETED', 'CANCELLED', 'PENDING'],
  'COMPLETED' => ['COMPLETED', 'PENDING', 'IN_PROGRESS', 'CANCELLED'],
  'CANCELLED' => ['CANCELLED', 'PENDING', 'IN_PROGRESS']
 ];

 // Check for specific invalid transitions
 if ($currentStatus === 'PENDING' && $order_status === 'COMPLETED') {
  throw new Exception('Cannot set order status to COMPLETED while it is currently PENDING. Please set it to IN_PROGRESS first.');
 }

 if ($currentStatus === 'PENDING' && $order_status === 'CANCELLED') {
  throw new Exception('Cannot set order status to CANCELLED while it is currently PENDING. Please set it to IN_PROGRESS first or you can delete the order directly.');
 }

 // General validation of status transitions
 if (!in_array($order_status, $validStatusTransitions[$currentStatus])) {
  throw new Exception("Invalid status transition from $currentStatus to $order_status");
 }

 // Initialize the update query
 $updateQuery = "UPDATE orders 
                    SET 
                    order_name = ?, 
                    project_manager_id = ?, 
                    start_date = ?,  
                    deadline = ?, 
                    status = ?";

 // Reset finished_at if rolling back from COMPLETED
 if ($currentStatus === 'COMPLETED') {
  $updateQuery .= ", finished_at = NULL";
 }

 // Add description if provided
 if (!empty($description)) {
  $updateQuery .= ", description = ?";
 }

 // Add order price if provided
 if (!empty($order_price)) {
  $updateQuery .= ", order_price = ?";
 }

 // Add worker assignment
 $updateQuery .= ", worker_id = " . ($assigned_worker !== 'NULL' ? "?" : "NULL");

 // Complete the query
 $updateQuery .= " WHERE id_order = ?";

 // Prepare the statement
 $updateStmt = mysqli_prepare($conn, $updateQuery);

 // Determine parameter types and bind parameters
 $paramTypes = "sssss"; // Initial types for required fields
 $params = [&$order_name, &$project_manager_id, &$start_date, &$deadline, &$order_status]; // Use references

 // Add optional parameters
 if (!empty($description)) {
  $paramTypes .= "s";
  $params[] = &$description; // Use reference
 }

 if (!empty($order_price)) {
  $paramTypes .= "s";
  $params[] = &$order_price; // Use reference
 }

 if ($assigned_worker !== 'NULL') {
  $paramTypes .= "i"; // Assuming assigned_worker is an integer
  $params[] = &$assigned_worker; // Use reference
 }

 // Add order_id as the last parameter
 $paramTypes .= "i";
 $params[] = &$order_id; // Use reference

 // Use call_user_func_array to dynamically bind parameters
 array_unshift($params, $updateStmt, $paramTypes); // Add statement and types at the beginning
 call_user_func_array('mysqli_stmt_bind_param', $params);

 // Execute the update
 if (!mysqli_stmt_execute($updateStmt)) {
  throw new Exception('Failed to update order: ' . mysqli_stmt_error($updateStmt));
 }

 // Commit the transaction
 mysqli_commit($conn);

 // Return success response
 echo json_encode(["success" => true, "message" => "Order updated successfully", "order_id" => $order_id]);

} catch (Exception $e) {
 // Rollback the transaction in case of error
 mysqli_rollback($conn);
 echo json_encode(["success" => false, "message" => $e->getMessage()]);
} finally {
 // Close the statement and connection
 if (isset($updateStmt)) {
  mysqli_stmt_close($updateStmt);
 }
 mysqli_close($conn);
}