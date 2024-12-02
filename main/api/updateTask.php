<?php
session_start();
require_once '../../config/database.php';
require_once '../common/allowedRoles.php';

// Check if user is a worker
if ($_SESSION['level'] !== 'worker') {
 echo json_encode([
  'success' => false,
  'message' => 'Unauthorized access'
 ]);
 exit;
}

// Validate input
$taskId = $_POST['task_id'] ?? null;
$status = $_POST['status'] ?? null;

if (!$taskId || !$status) {
 echo json_encode([
  'success' => false,
  'message' => 'Invalid input'
 ]);
 exit;
}

// Prepare SQL to update task status
$query = "UPDATE orders 
          SET status = ?,
          WHERE id_order = ? 
          AND worker_id = ?";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "sii", $status, $taskId, $_SESSION['user_id']);

if (mysqli_stmt_execute($stmt)) {
 echo json_encode([
  'success' => true,
  'message' => 'Task status updated successfully'
 ]);
} else {
 echo json_encode([
  'success' => false,
  'message' => 'Failed to update task status'
 ]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);