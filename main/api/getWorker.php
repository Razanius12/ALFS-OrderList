<?php
// Required headers
header('Content-Type: application/json');

// Include database connection
require_once '../../config/database.php';

try {
 // Check if id_worker is provided
 if (isset($_GET['id_worker'])) {
  // Sanitize input
  $id_worker = mysqli_real_escape_string($conn, $_GET['id_worker']);

  // Prepare SQL query
  $query = "SELECT 
            id_worker, 
            name_worker, 
            username, 
            id_position, 
            gender_worker, 
            phone_number,
            password
            FROM workers 
            WHERE id_worker = '$id_worker'";

  // Execute query
  $result = mysqli_query($conn, $query);

  // Check if query was successful
  if ($result) {
   // Fetch worker data
   $worker = mysqli_fetch_assoc($result);

   if ($worker) {
    // Return worker data as JSON
    echo json_encode([
     'success' => true,
     'data' => $worker
    ]);
   } else {
    // Worker not found
    echo json_encode([
     'success' => false,
     'message' => 'Worker not found'
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
  // No worker ID provided
  echo json_encode([
   'success' => false,
   'message' => 'Worker ID is required'
  ]);
 }
} catch (Exception $e) {
 echo json_encode([
  'success' => false,
  'message' => $e->getMessage()
 ]);
}
?>