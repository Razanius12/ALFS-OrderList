<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *"); // the "*" will later be replaced with the domain of the website
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

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
  $insertPositionQuery = "INSERT INTO positions (position_name, department) VALUES ('$newPositionName', 'WORKER')";
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
 $required_fields = ['username', 'name_worker', 'password', 'id_position', 'gender_worker', 'phone_number'];
 foreach ($required_fields as $field) {
  if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
   throw new Exception("$field is required");
  }
 }

 // Sanitize and validate input
 $username = mysqli_real_escape_string($conn, trim($_POST['username']));
 $name_worker = mysqli_real_escape_string($conn, trim($_POST['name_worker']));
 $password = trim($_POST['password']);
 $id_position = (int) $_POST['id_position'];
 $gender_worker = mysqli_real_escape_string($conn, trim($_POST['gender_worker']));
 $phone_number = mysqli_real_escape_string($conn, trim($_POST['phone_number']));

 // Validate username format (alphanumeric and underscores only)
 if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
  throw new Exception('Username can only contain letters, numbers, and underscores');
 }

 // Validate phone number format (adjust regex according to your needs)
 if (!preg_match('/^[0-9+\-\s()]{8,16}$/', $phone_number)) {
  throw new Exception('Invalid phone number format');
 }

 // Validate gender
 $allowed_genders = ['MALE', 'FEMALE', 'OTHER'];
 if (!in_array($gender_worker, $allowed_genders)) {
  throw new Exception('Invalid gender value');
 }

 // Check if username already exists
 $check_query = "SELECT id_worker FROM workers WHERE username = ?";
 $stmt = mysqli_prepare($conn, $check_query);
 mysqli_stmt_bind_param($stmt, "s", $username);
 mysqli_stmt_execute($stmt);
 mysqli_stmt_store_result($stmt);

 if (mysqli_stmt_num_rows($stmt) > 0) {
  throw new Exception('Username already exists');
 }
 mysqli_stmt_close($stmt);

 // Validate position exists and is a worker position
 $position_query = "SELECT id_position FROM positions WHERE id_position = ? AND department = 'WORKER'";
 $stmt = mysqli_prepare($conn, $position_query);
 mysqli_stmt_bind_param($stmt, "i", $id_position);
 mysqli_stmt_execute($stmt);
 mysqli_stmt_store_result($stmt);

 if (mysqli_stmt_num_rows($stmt) === 0) {
  throw new Exception('Invalid position selected');
 }
 mysqli_stmt_close($stmt);

 // Hash password
 $hashed_password = password_hash($password, PASSWORD_DEFAULT);

 // Insert new worker
 $insert_query = "INSERT INTO workers (username, name_worker, password, id_position, gender_worker, phone_number) 
                     VALUES (?, ?, ?, ?, ?, ?)";

 $stmt = mysqli_prepare($conn, $insert_query);
 mysqli_stmt_bind_param(
  $stmt,
  "sssiss",
  $username,
  $name_worker,
  $hashed_password,
  $id_position,
  $gender_worker,
  $phone_number
 );

 if (mysqli_stmt_execute($stmt)) {
  $response['success'] = true;
  $response['message'] = 'Worker added successfully';
  $response['data'] = [
   'id_worker' => mysqli_insert_id($conn),
   'username' => $username,
   'name_worker' => $name_worker
  ];
 } else {
  throw new Exception('Failed to add worker: ' . mysqli_error($conn));
 }

 mysqli_stmt_close($stmt);

} catch (Exception $e) {
 $response['success'] = false;
 $response['message'] = $e->getMessage();
} finally {
 mysqli_close($conn);
 echo json_encode($response);
}
?>