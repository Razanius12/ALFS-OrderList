<?php
// Required headers
header('Content-Type: application/json');

// Include database connection
require_once '../../config/database.php';

try {
 // Ensure all required fields are present
 $requiredFields = ['id_admin', 'username', 'name_admin', 'id_position', 'phone_number'];
 foreach ($requiredFields as $field) {
  if (!isset($_POST[$field]) || empty($_POST[$field])) {
   throw new Exception("Missing required field: $field");
  }
 }

 // Sanitize inputs
 $id_admin = mysqli_real_escape_string($conn, $_POST['id_admin']);
 $username = mysqli_real_escape_string($conn, $_POST['username']);
 $name_admin = mysqli_real_escape_string($conn, $_POST['name_admin']);
 $id_position = mysqli_real_escape_string($conn, $_POST['id_position']);
 $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);

 // Handle password update (without hashing for debugging)
 $passwordUpdate = '';
 if (!empty($_POST['password'])) {
  // Use the plain text password (not recommended for production)
  $password = mysqli_real_escape_string($conn, $_POST['password']);
  $passwordUpdate = ", password = '$password'";
 }

 // Prepare UPDATE query
 $query = "UPDATE admins 
           SET 
            username = '$username', 
            name_admin = '$name_admin', 
            id_position = '$id_position', 
            phone_number = '$phone_number'
            $passwordUpdate
           WHERE id_admin = '$id_admin'";

 // Execute query
 if (mysqli_query($conn, $query)) {
  echo json_encode([
   'success' => true,
   'message' => 'Admin updated successfully'
  ]);
 } else {
  throw new Exception('Failed to update admin: ' . mysqli_error($conn));
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