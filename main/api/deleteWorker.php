<?php
// Required headers
header('Content-Type: application/json');

// Include database connection
require_once '../../config/database.php';

// Set content type to JSON
header('Content-Type: application/json');

// Response array
$response = [
 'success' => false,
 'message' => 'Unknown error occurred'
];

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 // Get the worker ID from the POST request
 $id_worker = isset($_POST['id_worker']) ? intval($_POST['id_worker']) : 0;

 // Validate worker ID
 if ($id_worker <= 0) {
  $response['message'] = 'Invalid worker ID';
  echo json_encode($response);
  exit;
 }

 // Start a transaction
 mysqli_begin_transaction($conn);

 try {
  // First, remove any related project assignments
  $assignmentQuery = "DELETE FROM project_assignments WHERE id_worker = ?";
  $assignmentStmt = mysqli_prepare($conn, $assignmentQuery);
  mysqli_stmt_bind_param($assignmentStmt, "i", $id_worker);
  mysqli_stmt_execute($assignmentStmt);

  // Then delete the worker
  $workerQuery = "DELETE FROM workers WHERE id_worker = ?";
  $workerStmt = mysqli_prepare($conn, $workerQuery);
  mysqli_stmt_bind_param($workerStmt, "i", $id_worker);
  mysqli_stmt_execute($workerStmt);

  // Check if any rows were affected
  if (mysqli_stmt_affected_rows($workerStmt) > 0) {
   // Commit the transaction
   mysqli_commit($conn);

   $response['success'] = true;
   $response['message'] = 'Worker deleted successfully';
  } else {
   // Rollback the transaction
   mysqli_rollback($conn);

   $response['message'] = 'Worker not found or already deleted';
  }

  // Close statements
  mysqli_stmt_close($assignmentStmt);
  mysqli_stmt_close($workerStmt);
 } catch (Exception $e) {
  // Rollback the transaction in case of any error
  mysqli_rollback($conn);

  $response['message'] = 'Error deleting worker: ' . $e->getMessage();
 }
} else {
 $response['message'] = 'Invalid request method';
}

// Send JSON response
echo json_encode($response);

// Close the database connection
mysqli_close($conn);
exit;