<?php
// Required headers
header('Content-Type: application/json');

// Database connection
require_once '../../config/database.php';

// Initialize response array
$response = [
 'success' => false,
 'message' => '',
 'data' => null
];

try {
 // Check if request method is POST
 if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  throw new Exception('Invalid request method');
 }

 // Validate required fields
 $required_fields = ['order_name', 'project_manager_id', 'start_date', 'worker_id'];
 foreach ($required_fields as $field) {
  if (!isset($_POST[$field]) || empty($_POST[$field])) {
   throw new Exception("$field is required");
  }
 }

 // Sanitize and validate input
 $order_name = mysqli_real_escape_string($conn, $_POST['order_name']);
 $project_manager_id = (int) $_POST['project_manager_id'];
 $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
 $worker_id = (int) $_POST['worker_id'];

 // Optional description
 $description = isset($_POST['description']) ? mysqli_real_escape_string($conn, $_POST['description']) : NULL;

 // Validate project manager ID
 $check_manager_query = "SELECT id_admin FROM admins WHERE id_admin = ?";
 $stmt = mysqli_prepare($conn, $check_manager_query);
 mysqli_stmt_bind_param($stmt, "i", $project_manager_id);
 mysqli_stmt_execute($stmt);
 mysqli_stmt_store_result($stmt);

 if (mysqli_stmt_num_rows($stmt) === 0) {
  throw new Exception('Invalid project manager selected');
 }
 mysqli_stmt_close($stmt);

 // Validate worker ID
 $check_worker_query = "SELECT id_worker FROM workers WHERE id_worker = ?";
 $stmt = mysqli_prepare($conn, $check_worker_query);
 mysqli_stmt_bind_param($stmt, "i", $worker_id);
 mysqli_stmt_execute($stmt);
 mysqli_stmt_store_result($stmt);

 if (mysqli_stmt_num_rows($stmt) === 0) {
  throw new Exception('Invalid worker selected');
 }
 mysqli_stmt_close($stmt);

 // Insert new order with default status as PENDING
 $insert_query = "INSERT INTO orders (
                  order_name, 
                  status, 
                  start_date, 
                  project_manager_id, 
                  description
                  ) VALUES (?, 'PENDING', ?, ?, ?)";

 $stmt = mysqli_prepare($conn, $insert_query);
 mysqli_stmt_bind_param(
  $stmt,
  "ssis",
  $order_name,
  $start_date,
  $project_manager_id,
  $description
 );

 if (mysqli_stmt_execute($stmt)) {
  $order_id = mysqli_insert_id($conn);

  // Insert worker assignment
  $assignment_query = "INSERT INTO project_assignments (id_order, id_worker) VALUES (?, ?)";
  $assignment_stmt = mysqli_prepare($conn, $assignment_query);
  mysqli_stmt_bind_param($assignment_stmt, "ii", $order_id, $worker_id);

  if (mysqli_stmt_execute($assignment_stmt)) {
   $response['success'] = true;
   $response['message'] = 'Order added successfully';
   $response['data'] = [
    'id_order' => $order_id,
    'order_name' => $order_name,
    'status' => 'PENDING'
   ];
  } else {
   throw new Exception('Failed to assign worker: ' . mysqli_error($conn));
  }

  mysqli_stmt_close($assignment_stmt);
 } else {
  throw new Exception('Failed to add order: ' . mysqli_error($conn));
 }

 mysqli_stmt_close($stmt);

} catch (Exception $e) {
 $response['success'] = false;
 $response['message'] = $e->getMessage();

 // Debug: Log the full exception
 error_log("Exception: " . $e->getMessage());
 error_log("Trace: " . $e->getTraceAsString());
} finally {
 mysqli_close($conn);

 // Ensure JSON is properly encoded
 echo json_encode($response, JSON_PRETTY_PRINT);
 exit;
}
?>