<?php
require_once '../config/database.php';

header('Content-Type: application/json');

// Get JSON input
$data = json_decode(file_get_contents('php://input'), true);

// Validate input data
if (!isset($data['order_name'], $data['client_name'], $data['project_manager_id'], $data['project_status'], $data['start_date'], $data['assigned_workers'])) {
 echo json_encode(['error' => 'Missing required fields']);
 exit;
}

$query = "INSERT INTO orders (order_name, client_name, project_manager_id, project_status, start_date, assigned_workers)
          VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param('ssisss', $data['order_name'], $data['client_name'], $data['project_manager_id'], $data['project_status'], $data['start_date'], $data['assigned_workers']);

if ($stmt->execute()) {
 echo json_encode(['success' => true, 'message' => 'Order added successfully']);
} else {
 echo json_encode(['success' => false, 'message' => 'Failed to add order']);
}

$stmt->close();
$conn->close();
?>