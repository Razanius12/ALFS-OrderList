<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

ob_clean();

require_once '../../config/database.php';

try {
 // Check if ID is provided
 if (!isset($_GET['id']) || empty($_GET['id'])) {
  http_response_code(400);
  echo json_encode([
   'success' => false,
   'message' => 'Position ID is required'
  ]);
  exit;
 }

 $position_id = intval($_GET['id']);

 // Prepare delete statement
 $delete_sql = "DELETE FROM positions WHERE id_position = ?";
 $stmt = $conn->prepare($delete_sql);
 $stmt->bind_param("i", $position_id);

 if ($stmt->execute()) {
  if ($stmt->affected_rows > 0) {
   echo json_encode([
    'success' => true,
    'message' => 'Position deleted successfully'
   ]);
  } else {
   http_response_code(404);
   echo json_encode([
    'success' => false,
    'message' => 'Position not found or could not be deleted'
   ]);
  }
 } else {
  http_response_code(500);
  echo json_encode([
   'success' => false,
   'message' => 'Error deleting position: ' . $stmt->error
  ]);
 }

 $stmt->close();
 $conn->close();
} catch (Exception $e) {
 http_response_code(500);
 echo json_encode([
  'success' => false,
  'message' => 'Unexpected error: ' . $e->getMessage()
 ]);
}
exit;
?>