<?php
// Required headers
header('Content-Type: application/json');

// Include database connection
require_once '../../config/database.php';

try {
 // Check if id_admin is provided
 if (isset($_GET['id_admin'])) {
  // Sanitize input
  $id_admin = mysqli_real_escape_string($conn, $_GET['id_admin']);

  // Prepare SQL query
  $query = "SELECT 
             id_admin, 
             name_admin, 
             username, 
             id_position, 
             phone_number,
             password
             FROM admins 
             WHERE id_admin = '$id_admin'";

  // Execute query
  $result = mysqli_query($conn, $query);

  // Check if query was successful
  if ($result) {
   // Fetch admin data
   $admin = mysqli_fetch_assoc($result);

   if ($admin) {
    // Return admin data as JSON
    echo json_encode([
     'success' => true,
     'data' => $admin
    ]);
   } else {
    // Admin not found
    echo json_encode([
     'success' => false,
     'message' => 'Admin not found'
    ]);
   }

   // Free result set
   mysqli_free_result($result);
  } else {
   // Query failed
   echo json_encode([
    'success' => false,
    'message' => 'Error executing query: ' . mysqli_error($conn)
   ]);
  }
 } else {
  // No admin ID provided
  echo json_encode([
   'success' => false,
   'message' => 'Admin ID is required'
  ]);
 }
} catch (Exception $e) {
 echo json_encode([
  'success' => false,
  'message' => $e->getMessage()
 ]);
}
?>