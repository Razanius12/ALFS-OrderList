<?php
// Required headers
header('Content-Type: application/json');

// Database connection
require_once '../../config/database.php';

// Initialize response array
$response = [
 'success' => false,
 'message' => ''
];

try {
 // Check if request method is POST
 if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  throw new Exception('Invalid request method');
 }

 // Validate maps ID
 if (!isset($_POST['id_maps']) || empty($_POST['id_maps'])) {
  throw new Exception('Maps ID is required');
 }

 // Sanitize maps ID
 $maps_id = (int) $_POST['id_maps'];

 // Check if maps entry exists before deletion
 $check_query = "SELECT id_maps, name_city_district FROM gmaps WHERE id_maps = ?";
 $stmt = mysqli_prepare($conn, $check_query);
 mysqli_stmt_bind_param($stmt, "i", $maps_id);
 mysqli_stmt_execute($stmt);
 mysqli_stmt_store_result($stmt);

 if (mysqli_stmt_num_rows($stmt) === 0) {
  throw new Exception('Maps entry not found');
 }

 // Fetch city/district name for logging
 mysqli_stmt_bind_result($stmt, $id, $name_city_district);
 mysqli_stmt_fetch($stmt);
 mysqli_stmt_close($stmt);

 // Prepare delete query
 $delete_query = "DELETE FROM gmaps WHERE id_maps = ?";
 $stmt = mysqli_prepare($conn, $delete_query);
 mysqli_stmt_bind_param($stmt, "i", $maps_id);

 if (mysqli_stmt_execute($stmt)) {
  // Check if any rows were actually deleted
  if (mysqli_stmt_affected_rows($stmt) > 0) {
   $response['success'] = true;
   $response['message'] = "Maps entry for '{$name_city_district}' deleted successfully";
  } else {
   throw new Exception('No maps entry was deleted');
  }
 } else {
  throw new Exception('Failed to delete maps entry: ' . mysqli_error($conn));
 }

 mysqli_stmt_close($stmt);

} catch (Exception $e) {
 $response['success'] = false;
 $response['message'] = $e->getMessage();

 // Debug: Log the full exception
 error_log("Exception in deleteAlfOffices: " . $e->getMessage());
 error_log("Trace: " . $e->getTraceAsString());
} finally {
 mysqli_close($conn);

 // Ensure JSON is properly encoded
 echo json_encode($response);
 exit;
}
?>