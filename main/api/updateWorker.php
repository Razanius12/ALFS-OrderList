<?php
// Required headers
header('Content-Type: application/json');

// Include database connection
require_once '../../config/database.php';

try {
 // Ensure all required fields are present
 $requiredFields = ['id_worker', 'username', 'name_worker', 'id_position', 'gender_worker', 'phone_number'];
 foreach ($requiredFields as $field) {
  if (!isset($_POST[$field]) || empty($_POST[$field])) {
   throw new Exception("Missing required field: $field");
  }
 }

 // Sanitize inputs
 $id_worker = mysqli_real_escape_string($conn, $_POST['id_worker']);
 $username = mysqli_real_escape_string($conn, $_POST['username']);
 $name_worker = mysqli_real_escape_string($conn, $_POST['name_worker']);
 $id_position = mysqli_real_escape_string($conn, $_POST['id_position']);
 $gender_worker = mysqli_real_escape_string($conn, $_POST['gender_worker']);
 $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);

 // Handle password update (without hashing for debugging)
 $passwordUpdate = '';
 if (!empty($_POST['password'])) {
  // Use the plain text password (not recommended for production)
  $password = mysqli_real_escape_string($conn, $_POST['password']);
  $passwordUpdate = ", password = '$password'";
 }

 // Prepare UPDATE query
 $query = "UPDATE workers 
            SET 
             username = '$username', 
             name_worker = '$name_worker', 
             id_position = '$id_position', 
             gender_worker = '$gender_worker', 
             phone_number = '$phone_number'
             $passwordUpdate
            WHERE id_worker = '$id_worker'";

 // Execute query
 if (mysqli_query($conn, $query)) {
  echo json_encode([
   'success' => true,
   'message' => 'Worker updated successfully'
  ]);
 } else {
  throw new Exception('Failed to update worker: ' . mysqli_error($conn));
 }
} catch (Exception $e) {
 // Error handling
 echo json_encode([
  'success' => false,
  'message' => $e->getMessage()
 ]);
}

// Close connection
mysqli_close($conn);
?>