<?php
// Include database connection
require_once 'config/database.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Prevent direct access
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
 http_response_code(403);
 echo json_encode(['error' => 'Forbidden']);
 exit();
}

// Sanitize input
$position_name = trim($_POST['position_name'] ?? '');
$department = $_POST['department'] ?? 'WORKER'; // Default department

// Validate input
if (empty($position_name)) {
 echo json_encode([
  'success' => false,
  'message' => 'Position name cannot be empty'
 ]);
 exit();
}

try {
 // Prevent SQL injection
 $position_name = mysqli_real_escape_string($conn, $position_name);
 $department = mysqli_real_escape_string($conn, $department);

 // Check if position already exists (case-insensitive)
 $checkQuery = "SELECT id_position 
                   FROM positions 
                   WHERE LOWER(position_name) = LOWER('$position_name')";

 $checkResult = mysqli_query($conn, $checkQuery);

 if (mysqli_num_rows($checkResult) > 0) {
  // Position already exists
  $existingPosition = mysqli_fetch_assoc($checkResult);
  echo json_encode([
   'success' => true,
   'id_position' => $existingPosition['id_position'],
   'message' => 'Position already exists'
  ]);
  exit();
 }

 // Insert new position
 $insertQuery = "INSERT INTO positions (position_name, department) 
                    VALUES ('$position_name', '$department')";

 if (mysqli_query($conn, $insertQuery)) {
  // Get the ID of the newly inserted position
  $new_position_id = mysqli_insert_id($conn);

  echo json_encode([
   'success' => true,
   'id_position' => $new_position_id,
   'message' => 'Position created successfully'
  ]);
 } else {
  throw new Exception("Failed to insert position: " . mysqli_error($conn));
 }
} catch (Exception $e) {
 // Handle any errors
 http_response_code(500);
 echo json_encode([
  'success' => false,
  'message' => 'An error occurred: ' . $e->getMessage()
 ]);
} finally {
 // Close database connection
 mysqli_close($conn);
}