<?php
// Required headers
header("Content-Type: application/json");

// Database connection
require_once '../../config/database.php';

$response = ['success' => false, 'message' => 'Unknown error'];

try {
 // Ensure all required fields are present
 $requiredFields = ['id_position', 'position_name', 'department'];
 foreach ($requiredFields as $field) {
  if (!isset($_POST[$field]) || empty($_POST[$field])) {
   throw new Exception("Missing required field: $field");
  }
 }

 // Sanitize inputs
 $id_position = mysqli_real_escape_string($conn, $_POST['id_position']);
 $position_name = mysqli_real_escape_string($conn, $_POST['position_name']);
 $department = mysqli_real_escape_string($conn, $_POST['department']);

 // Validate department
 if (!in_array($department, ['ADMIN', 'WORKER'])) {
  throw new Exception("Invalid department selected");
 }

 // Validate position name length
 if (strlen($position_name) > 32) {
  throw new Exception("Position name must be less than 32 characters");
 }

 // Prepare UPDATE query
 $query = "UPDATE positions 
           SET 
             position_name = '$position_name', 
             department = '$department'
           WHERE id_position = '$id_position'";

 // Execute query
 if (mysqli_query($conn, $query)) {
  echo json_encode([
   'success' => true,
   'message' => 'Position updated successfully'
  ]);
 } else {
  throw new Exception('Failed to update position: ' . mysqli_error($conn));
 }
} catch (Exception $e) {
 // Error handling
 echo json_encode([
  'success' => false,
  'message' => $e->getMessage()
 ]);
}
?>