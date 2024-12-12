<?php
// Required headers
header('Content-Type: application/json');

// Include database connection
require_once '../../config/database.php';

try {
 // Check if id_maps is provided (optional)
 if (isset($_GET['id_maps'])) {
  // Sanitize input
  $id_maps = mysqli_real_escape_string($conn, $_GET['id_maps']);

  // Prepare SQL query with optional filtering
  $query = "SELECT id_maps, name_city_district, link_embed 
            FROM gmaps 
            " . ($id_maps ? "WHERE id_maps = '$id_maps'" : "");

  // Execute query
  $result = mysqli_query($conn, $query);

  // Check if query was successful
  if ($result) {
   // Check if any rows were returned
   if (mysqli_num_rows($result) > 0) {
    // Fetch offices data
    $offices = [];
    while ($row = mysqli_fetch_assoc($result)) {
     $offices[] = $row;
    }

    // Return offices data as JSON
    echo json_encode([
     'success' => true,
     'data' => count($offices) === 1 ? $offices[0] : $offices
    ]);
   } else {
    // No offices found
    echo json_encode([
     'success' => false,
     'message' => 'No offices found'
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
  // Fetch all offices if no specific ID is provided
  $query = "SELECT id_maps, name_city_district, link_embed FROM gmaps";

  // Execute query
  $result = mysqli_query($conn, $query);

  // Check if query was successful
  if ($result) {
   // Check if any rows were returned
   if (mysqli_num_rows($result) > 0) {
    // Fetch offices data
    $offices = [];
    while ($row = mysqli_fetch_assoc($result)) {
     $offices[] = $row;
    }

    // Return offices data as JSON
    echo json_encode([
     'success' => true,
     'data' => $offices
    ]);
   } else {
    // No offices found
    echo json_encode([
     'success' => false,
     'message' => 'No offices found'
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
 }
} catch (Exception $e) {
 echo json_encode([
  'success' => false,
  'message' => $e->getMessage()
 ]);
}
?>