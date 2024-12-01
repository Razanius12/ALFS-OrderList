<?php
// Required headers
header('Content-Type: application/json');

// Include database connection
require_once '../../config/database.php';

try {
 // Prepare SQL query to get admin contacts
 $query = "SELECT 
           id_admin, 
           name_admin, 
           phone_number
           FROM admins";

 // Execute query
 $result = mysqli_query($conn, $query);

 // Check if query was successful
 if ($result) {
  // Fetch all admin contacts
  $admins = [];
  while ($admin = mysqli_fetch_assoc($result)) {
   // Only include admins with phone numbers
   if (!empty($admin['phone_number'])) {
    $admins[] = $admin;
   }
  }

  if (!empty($admins)) {
   // Return admin contacts as JSON
   echo json_encode([
    'success' => true,
    'data' => $admins
   ]);
  } else {
   // No admins with phone numbers found
   echo json_encode([
    'success' => false,
    'message' => 'No admin contacts available'
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
} catch (Exception $e) {
 echo json_encode([
  'success' => false,
  'message' => $e->getMessage()
 ]);
}
?>