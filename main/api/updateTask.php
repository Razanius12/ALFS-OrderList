<?php
session_start();
require_once '../../config/database.php';

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

// Start a transaction for atomic operations
mysqli_begin_transaction($conn);

try {
 // Sanitize inputs
 $taskId = mysqli_real_escape_string($conn, $taskId);
 $status = mysqli_real_escape_string($conn, $status);
 $workerId = $_SESSION['user_id'];

 // First, verify the task is actually assigned to the worker
 $checkAssignmentQuery = "SELECT id_order 
                          FROM orders 
                          WHERE id_order = ? 
                          AND worker_id = ?";

 $checkStmt = mysqli_prepare($conn, $checkAssignmentQuery);
 mysqli_stmt_bind_param($checkStmt, "ii", $taskId, $workerId);
 mysqli_stmt_execute($checkStmt);
 $checkResult = mysqli_stmt_get_result($checkStmt);

 if (mysqli_num_rows($checkResult) === 0) {
  throw new Exception('Task not found or not assigned to you');
 }

 // Reset worker's assignment and unlink worker from order in one query
 $resetWorkerQuery = "UPDATE workers w
                      JOIN orders o ON w.assigned_order_id = o.id_order
                      SET 
                       w.availability_status = 'AVAILABLE', 
                       w.assigned_order_id = NULL,
                       o.worker_id = NULL
                      WHERE o.id_order = ?";

 $resetStmt = mysqli_prepare($conn, $resetWorkerQuery);
 mysqli_stmt_bind_param($resetStmt, "i", $taskId);
 if (!mysqli_stmt_execute($resetStmt)) {
  throw new Exception('Failed to update worker status: ' . mysqli_error($conn));
 }

 // Update order status and reset worker assignment
 $updateOrderQuery = "UPDATE orders 
                      SET status = ?, 
                       worker_id = NULL 
                      WHERE id_order = ?";

 $stmt = mysqli_prepare($conn, $updateOrderQuery);
 mysqli_stmt_bind_param($stmt, "si", $status, $taskId);

 if (!mysqli_stmt_execute($stmt)) {
  throw new Exception('Failed to update task status: ' . mysqli_stmt_error($stmt));
 }

 // Commit the transaction
 mysqli_commit($conn);

 echo json_encode([
  'success' => true,
  'message' => 'The task has been successfully marked as completed. You will not see the task again when it\'s completed.'
 ]);
} catch (Exception $e) {
 // Rollback the transaction in case of error
 mysqli_rollback($conn);

 // Error handling
 http_response_code(400); // Bad Request
 echo json_encode([
  'success' => false,
  'message' => $e->getMessage()
 ]);
}

// Close statements and connection
mysqli_stmt_close($checkStmt);
mysqli_stmt_close($resetStmt);
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>