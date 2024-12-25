<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/session.php';

// Check if user is a worker
if ($_SESSION['level'] !== 'worker') {
 header('Content-Type: application/json');
 echo json_encode([
  'success' => false,
  'message' => 'Unauthorized access'
 ]);
 exit;
}

// Validate input
$taskId = $_POST['task_id'] ?? null;

if (!$taskId) {
 header('Content-Type: application/json');
 echo json_encode([
  'success' => false,
  'message' => 'Invalid input'
 ]);
 exit;
}

try {
 // Begin transaction
 mysqli_begin_transaction($conn);

 // Check current order status
 $checkStatusQuery = "SELECT status FROM orders WHERE id_order = ?";  // Removed incorrect AND clause
 $stmt = mysqli_prepare($conn, $checkStatusQuery);
 mysqli_stmt_bind_param($stmt, "i", $taskId);
 mysqli_stmt_execute($stmt);
 $result = mysqli_stmt_get_result($stmt);
 $order = mysqli_fetch_assoc($result);

 if (!$order || $order['status'] !== 'PENDING') {
  throw new Exception('This task cannot be taken. It may already be assigned or not in PENDING status.');
 }

 // Update order to IN_PROGRESS and assign to current worker
 $updateQuery = "UPDATE orders 
                 SET status = 'IN_PROGRESS', 
                  worker_id = ? 
                 WHERE id_order = ?";
 $updateStmt = mysqli_prepare($conn, $updateQuery);
 mysqli_stmt_bind_param($updateStmt, "ii", $_SESSION['user_id'], $taskId);

 if (!mysqli_stmt_execute($updateStmt)) {
  throw new Exception('Failed to take the task');
 }

 // Update worker's status
 $workerUpdateQuery = "UPDATE workers 
                       SET availability_status = 'TASKED', 
                        assigned_order_id = ? 
                       WHERE id_worker = ?";
 $workerStmt = mysqli_prepare($conn, $workerUpdateQuery);
 mysqli_stmt_bind_param($workerStmt, "ii", $taskId, $_SESSION['user_id']);

 if (!mysqli_stmt_execute($workerStmt)) {
  throw new Exception('Failed to update worker status');
 }

 // Commit transaction
 mysqli_commit($conn);

 header('Content-Type: application/json');
 echo json_encode([
  'success' => true,
  'message' => 'Task successfully taken'
 ]);

} catch (Exception $e) {
 // Rollback transaction
 mysqli_rollback($conn);

 header('Content-Type: application/json');
 echo json_encode([
  'success' => false,
  'message' => $e->getMessage()
 ]);
}

mysqli_close($conn);