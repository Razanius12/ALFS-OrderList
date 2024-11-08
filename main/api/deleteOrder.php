<?php
require_once '../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_order'])) {
 $id_order = $_POST['id_order'];

 // Start transaction
 mysqli_begin_transaction($conn);

 try {
  // Delete project assignments first
  $delete_assignments = "DELETE FROM project_assignments WHERE id_order = ?";
  $stmt = mysqli_prepare($conn, $delete_assignments);
  mysqli_stmt_bind_param($stmt, "i", $id_order);
  mysqli_stmt_execute($stmt);

  // Then delete the order
  $delete_order = "DELETE FROM orders WHERE id_order = ?";
  $stmt = mysqli_prepare($conn, $delete_order);
  mysqli_stmt_bind_param($stmt, "i", $id_order);
  mysqli_stmt_execute($stmt);

  mysqli_commit($conn);
  echo json_encode(['success' => true]);

 } catch (Exception $e) {
  mysqli_rollback($conn);
  echo json_encode([
   'success' => false,
   'message' => 'Failed to delete order: ' . $e->getMessage()
  ]);
 }
} else {
 echo json_encode([
  'success' => false,
  'message' => 'Invalid request'
 ]);
}