<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Required headers
header('Content-Type: application/json');

// Include database connection
require_once '../../config/database.php';

// Response array
$response = [
 'success' => false,
 'message' => 'Unknown error occurred'
];

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

 // Start transaction
 mysqli_begin_transaction($conn);

 // Check for related workers
 $checkWorkersQuery = "SELECT COUNT(*) as count FROM workers WHERE id_position = ?";
 $checkWorkersStmt = mysqli_prepare($conn, $checkWorkersQuery);
 mysqli_stmt_bind_param($checkWorkersStmt, "i", $position_id);
 mysqli_stmt_execute($checkWorkersStmt);
 mysqli_stmt_bind_result($checkWorkersStmt, $workersCount);
 mysqli_stmt_fetch($checkWorkersStmt);
 mysqli_stmt_close($checkWorkersStmt);

 if ($workersCount > 0) {
  mysqli_rollback($conn);
  echo json_encode([
   'success' => false,
   'message' => 'Cannot delete position: There are workers assigned to this position'
  ]);
  exit;
 }

 // Check for related admins
 $checkAdminsQuery = "SELECT COUNT(*) as count FROM admins WHERE id_position = ?";
 $checkAdminsStmt = mysqli_prepare($conn, $checkAdminsQuery);
 mysqli_stmt_bind_param($checkAdminsStmt, "i", $position_id);
 mysqli_stmt_execute($checkAdminsStmt);
 mysqli_stmt_bind_result($checkAdminsStmt, $adminsCount);
 mysqli_stmt_fetch($checkAdminsStmt);
 mysqli_stmt_close($checkAdminsStmt);

 if ($adminsCount > 0) {
  mysqli_rollback($conn);
  echo json_encode([
   'success' => false,
   'message' => 'Cannot delete position: There are admins assigned to this position'
  ]);
  exit;
 }

 // If no related records found, proceed with deletion
 $deleteQuery = "DELETE FROM positions WHERE id_position = ?";
 $deleteStmt = mysqli_prepare($conn, $deleteQuery);
 mysqli_stmt_bind_param($deleteStmt, "i", $position_id);

 if (mysqli_stmt_execute($deleteStmt)) {
  if (mysqli_stmt_affected_rows($deleteStmt) > 0) {
   mysqli_commit($conn);
   echo json_encode([
    'success' => true,
    'message' => 'Position deleted successfully'
   ]);
  } else {
   mysqli_rollback($conn);
   http_response_code(404);
   echo json_encode([
    'success' => false,
    'message' => 'Position not found'
   ]);
  }
 } else {
  mysqli_rollback($conn);
  http_response_code(500);
  echo json_encode([
   'success' => false,
   'message' => 'Error deleting position: ' . mysqli_stmt_error($deleteStmt)
  ]);
 }

 mysqli_stmt_close($deleteStmt);

} catch (Exception $e) {
 mysqli_rollback($conn);
 http_response_code(500);
 echo json_encode([
  'success' => false,
  'message' => 'Unexpected error: ' . $e->getMessage()
 ]);
}

mysqli_close($conn);
exit;
?>