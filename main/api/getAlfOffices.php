<?php
// Required headers
header('Content-Type: application/json');

// Include database connection
require_once '../../config/database.php';

try {
 // Check if id_maps is provided (optional - for single office retrieval)
 if (isset($_GET['id_maps'])) {
  // Sanitize input
  $id_maps = mysqli_real_escape_string($conn, $_GET['id_maps']);

  // Prepare comprehensive SQL query for single office
  $query = "SELECT 
             id_maps, 
             name_city_district, 
             link_embed
            FROM 
             gmaps
            WHERE 
             id_maps = '$id_maps'";

  // Execute query
  $result = mysqli_query($conn, $query);

  // Check if query was successful
  if ($result) {
   // Fetch office data
   $office = mysqli_fetch_assoc($result);

   if ($office) {
    // Return office data as JSON
    echo json_encode([
     'success' => true,
     'data' => $office
    ]);
   } else {
    // Office not found
    echo json_encode([
     'success' => false,
     'message' => 'Office not found'
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
  // No specific ID - retrieve all offices
  $query = "SELECT 
             id_maps, 
             name_city_district, 
             link_embed
            FROM 
             gmaps";

  // Execute query
  $result = mysqli_query($conn, $query);

  // Check if query was successful
  if ($result) {
   // Fetch all offices
   $offices = [];
   while ($office = mysqli_fetch_assoc($result)) {
    $offices[] = $office;
   }

   // Prepare additional data for potential UI needs
   $additionalData = [
    'total_offices' => count($offices),
    'last_updated' => date('Y-m-d H:i:s')
   ];

   // Return offices data as JSON
   echo json_encode([
    'success' => true,
    'data' => $offices,
    'meta' => $additionalData
   ]);

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
} finally {
 // Close database connection
 if (isset($conn)) {
  mysqli_close($conn);
 }
}
?>