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
 $required_fields = ['order_name', 'project_manager_id', 'start_date', 'deadline'];
 foreach ($required_fields as $field) {
  if (!isset($_POST[$field]) || empty($_POST[$field])) {
   throw new Exception("$field is required");
  }
 }

 // Sanitize and validate input
 $order_name = mysqli_real_escape_string($conn, $_POST['order_name']);
 $project_manager_id = (int) $_POST['project_manager_id'];

 // Convert datetime-local input to MySQL datetime format
 $start_date_input = $_POST['start_date'];
 $start_date = date('Y-m-d H:i:s', strtotime($start_date_input));

 // Sanitize and validate the deadline input
 $deadline_input = $_POST['deadline'];
 $deadline = date('Y-m-d', strtotime($deadline_input));

 // Optional worker and description
 $worker_id = isset($_POST['assigned_worker']) && !empty($_POST['assigned_worker'])
  ? (int) $_POST['assigned_worker']
  : NULL;
 $description = isset($_POST['description']) ? mysqli_real_escape_string($conn, $_POST['description']) : NULL;
 $order_price = isset($_POST['order_price']) ? mysqli_real_escape_string($conn, $_POST['order_price']) : NULL;

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

 // Validate worker ID if provided
 if ($worker_id !== NULL) {
  $check_worker_query = "SELECT id_worker FROM workers WHERE id_worker = ? AND availability_status = 'AVAILABLE'";
  $stmt = mysqli_prepare($conn, $check_worker_query);
  mysqli_stmt_bind_param($stmt, "i", $worker_id);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_store_result($stmt);

  if (mysqli_stmt_num_rows($stmt) === 0) {
   throw new Exception('Invalid or unavailable worker selected');
  }
  mysqli_stmt_close($stmt);
 }

 // Insert new order query
 $insert_query = "INSERT INTO orders (
 order_name, 
 start_date, 
 deadline, 
 project_manager_id, 
 worker_id,
 description,
 status,
 order_price
) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

 $default_status = 'PENDING';

 // Ensure order price is an integer
 if (!empty($order_price) && !is_numeric($order_price)) {
  throw new Exception('Order price must be a valid number');
 }

 $order_price = intval($order_price); // Convert to integer

 $stmt = mysqli_prepare($conn, $insert_query);
 mysqli_stmt_bind_param(
  $stmt,
  "sssisssi",   // Adjust parameter types as needed
  $order_name,   // s
  $start_date,  // s
  $deadline,           // s
  $project_manager_id, // i
  $worker_id,          // i (nullable)
  $description,        // s (nullable)
  $default_status,     // s
  $order_price         // i
 );

 if (mysqli_stmt_execute($stmt)) {
  $order_id = mysqli_insert_id($conn);

  $response['success'] = true;
  $response['message'] = 'Order added successfully';
  $response['data'] = [
   'id_order' => $order_id,
   'order_name' => $order_name,
   'start_date' => $start_date,
   'deadline' => $deadline,
   'worker_assigned' => $worker_id ? 'Yes' : 'No'
  ];

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