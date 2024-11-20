<?php
require_once '../../config/database.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Unknown error'];

try {
 // Input validation
 $id_position = filter_input(INPUT_POST, 'id_position', FILTER_VALIDATE_INT);
 $position_name = trim($_POST['position_name'] ?? '');
 $department = $_POST['department'] ?? '';

 // Validate inputs
 $errors = [];

 if (!$id_position) {
  $errors[] = "Invalid position ID";
 }

 if (empty($position_name)) {
  $errors[] = "Position name is required";
 } elseif (strlen($position_name) > 32) {
  $errors[] = "Position name must be less than 32 characters";
 }

 if (empty($department) || !in_array($department, ['ADMIN', 'WORKER'])) {
  $errors[] = "Invalid department";
 }

 if (!empty($errors)) {
  $response['message'] = implode(', ', $errors);
  echo json_encode($response);
  exit;
 }

 // Prepare update statement
 $sql = "UPDATE positions SET position_name = ?, department = ? WHERE id_position = ?";

 $stmt = $conn->prepare($sql);
 $stmt->bind_param("ssi", $position_name, $department, $id_position);

 if ($stmt->execute()) {
  $response['success'] = true;
  $response['message'] = "Position updated successfully";
 } else {
  $response['message'] = "Error updating position: " . $stmt->error;
 }

 $stmt->close();
} catch (Exception $e) {
 $response['message'] = "Database error: " . $e->getMessage();
} finally {
 echo json_encode($response);
}