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

 // Check if position exists
 $checkQuery = "SELECT id_position, position_name 
                   FROM positions 
                   WHERE LOWER(position_name) = LOWER('$position_name')";

 $result = mysqli_query($conn, $checkQuery);

 if (!$result) {
  throw new Exception("Query failed: " . mysqli_error($conn));
 }

 // Check results
 if (mysqli_num_rows($result) > 0) {
  // Position exists
  $position = mysqli_fetch_assoc($result);
  echo json_encode([
   'success' => true,
   'exists' => true,
   'id_position' => $position['id_position'],
   'message' => 'Position already exists'
  ]);
 } else {
  // Position does not exist
  echo json_encode([
   'success' => true,
   'exists' => false,
   'message' => 'Position does not exist'
  ]);
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