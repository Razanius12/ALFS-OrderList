<?php
// delete_position.php
require_once 'config/database.php';

header('Content-Type: application/json');

try {
 // Validate input
 if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  throw new Exception('Invalid position ID');
 }

 $position_id = (int) $_GET['id'];

 // Check if position exists and is not in use
 $check_sql = "SELECT COUNT(*) as count FROM positions WHERE id_position = ?";
 $check_stmt = $conn->prepare($check_sql);
 $check_stmt->bind_param("i", $position_id);
 $check_stmt->execute();
 $result = $check_stmt->get_result();
 $row = $result->fetch_assoc();

 if ($row['count'] == 0) {
  throw new Exception('Position not found');
 }

 // Delete the position
 $delete_sql = "DELETE FROM positions WHERE id_position = ?";
 $delete_stmt = $conn->prepare($delete_sql);
 $delete_stmt->bind_param("i", $position_id);

 if (!$delete_stmt->execute()) {
  throw new Exception('Error deleting position: ' . $delete_stmt->error);
 }

 echo json_encode(['success' => true, 'message' => 'Position deleted successfully']);

} catch (Exception $e) {
 http_response_code(400);
 echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();