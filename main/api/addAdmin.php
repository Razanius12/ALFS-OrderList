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

// Check if a new position is being created
if (!empty($_POST['new_position'])) {
 // First, check if the position already exists
 $newPositionName = mysqli_real_escape_string($conn, $_POST['new_position']);
 $checkQuery = "SELECT id_position FROM positions WHERE position_name = '$newPositionName'";
 $checkResult = mysqli_query($conn, $checkQuery);

 if (mysqli_num_rows($checkResult) == 0) {
  // Insert new position
  $insertPositionQuery = "INSERT INTO positions (position_name, department) VALUES ('$newPositionName', 'ADMIN')";
  mysqli_query($conn, $insertPositionQuery);

  // Get the ID of the newly inserted position
  $_POST['id_position'] = mysqli_insert_id($conn);
 } else {
  // If position exists, fetch its ID
  $positionRow = mysqli_fetch_assoc($checkResult);
  $_POST['id_position'] = $positionRow['id_position'];
 }
}

try {
 // Check if request method is POST
 if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  throw new Exception('Invalid request method');
 }

 // Validate required fields
 $required_fields = ['username', 'name_admin', 'password', 'id_position', 'phone_number'];
 foreach ($required_fields as $field) {
  if (!isset($_POST[$field]) || empty($_POST[$field])) {
   throw new Exception("$field is required");
  }
 }

 // Sanitize and validate input
 $username = mysqli_real_escape_string($conn, $_POST['username']);
 $name_admin = mysqli_real_escape_string($conn, $_POST['name_admin']);
 $password = mysqli_real_escape_string($conn, $_POST['password']);
 $id_position = (int) $_POST['id_position'];
 $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);

 // Debug: Log received password
 error_log("Received password: " . $password);

 // Validate username format (alphanumeric and underscores only)
 if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
  throw new Exception('Username can only contain letters, numbers, and underscores');
 }

 // Check if username already exists
 $check_query = "SELECT id_admin FROM admins WHERE username = ?";
 $stmt = mysqli_prepare($conn, $check_query);
 mysqli_stmt_bind_param($stmt, "s", $username);
 mysqli_stmt_execute($stmt);
 mysqli_stmt_store_result($stmt);

 if (mysqli_stmt_num_rows($stmt) > 0) {
  throw new Exception('Username already exists');
 }
 mysqli_stmt_close($stmt);

 // Validate position exists and is an admin position
 $position_query = "SELECT id_position FROM positions WHERE id_position = ? AND department = 'ADMIN'";
 $stmt = mysqli_prepare($conn, $position_query);
 mysqli_stmt_bind_param($stmt, "i", $id_position);
 mysqli_stmt_execute($stmt);
 mysqli_stmt_store_result($stmt);

 if (mysqli_stmt_num_rows($stmt) === 0) {
  throw new Exception('Invalid position selected');
 }
 mysqli_stmt_close($stmt);

 // Insert new admin
 $insert_query = "INSERT INTO admins (username, name_admin, password, id_position, phone_number) 
                  VALUES (?, ?, ?, ?, ?)";

 $stmt = mysqli_prepare($conn, $insert_query);
 mysqli_stmt_bind_param(
  $stmt,
  "sssss",
  $username,
  $name_admin,
  $password,
  $id_position,
  $phone_number
 );

 if (mysqli_stmt_execute($stmt)) {
  $response['success'] = true;
  $response['message'] = 'Admin added successfully';
  $response['data'] = [
   'id_admin' => mysqli_insert_id($conn),
   'username' => $username,
   'name_admin' => $name_admin
  ];
 } else {
  throw new Exception('Failed to add admin: ' . mysqli_error($conn));
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
 exit; // Add exit to prevent any additional output
}
?>