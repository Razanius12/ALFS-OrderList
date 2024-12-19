<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

if (!isset($_GET['id_position'])) {
 echo json_encode(['success' => false, 'message' => 'Position ID is required']);
 exit;
}

$id_position = $_GET['id_position'];

$query = "SELECT id_position, position_name, department FROM positions WHERE id_position = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_position);

if ($stmt->execute()) {
 $result = $stmt->get_result();
 if ($position = $result->fetch_assoc()) {
  echo json_encode(['success' => true, 'data' => $position]);
 } else {
  echo json_encode(['success' => false, 'message' => 'Position not found']);
 }
} else {
 echo json_encode(['success' => false, 'message' => 'Database error']);
}

$stmt->close();
$conn->close();
?>