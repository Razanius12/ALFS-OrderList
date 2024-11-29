<?php
// Required headers
header('Content-Type: application/json');

// Include database connection
require_once '../../config/database.php';

// Response array
$response = [
 'success' => false,
 'message' => 'Unknown error occurred'
];

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 // Get the admin ID from the POST request
 $id_admin = isset($_POST['id_admin']) ? intval($_POST['id_admin']) : 0;

 // Validate admin ID
 if ($id_admin <= 0) {
  $response['message'] = 'Invalid admin ID';
  echo json_encode($response);
  exit;
 }

 // Start a transaction
 mysqli_begin_transaction($conn);

 try {
  // Check for related orders
  $checkOrdersQuery = "SELECT COUNT(*) as count FROM orders WHERE project_manager_id = ?";
  $checkStmt = mysqli_prepare($conn, $checkOrdersQuery);
  mysqli_stmt_bind_param($checkStmt, "i", $id_admin);
  mysqli_stmt_execute($checkStmt);
  mysqli_stmt_bind_result($checkStmt, $count);
  mysqli_stmt_fetch($checkStmt);
  mysqli_stmt_close($checkStmt);

  // If there are related orders, do not allow deletion
  if ($count > 0) {
   $response['message'] = 'Cannot delete admin with existing orders.';
   echo json_encode($response);
   exit;
  }

  // Proceed to delete the admin
  $adminQuery = "DELETE FROM admins WHERE id_admin = ?";
  $adminStmt = mysqli_prepare($conn, $adminQuery);
  mysqli_stmt_bind_param($adminStmt, "i", $id_admin);
  mysqli_stmt_execute($adminStmt);

  // Check if any rows were affected
  if (mysqli_stmt_affected_rows($adminStmt) > 0) {
   // Commit the transaction
   mysqli_commit($conn);

   $response['success'] = true;
   $response['message'] = 'Admin deleted successfully';
  } else {
   // Rollback the transaction
   mysqli_rollback($conn);

   $response['message'] = 'Admin not found or already deleted';
  }

  // Close statements
  mysqli_stmt_close($adminStmt);
 } catch (Exception $e) {
  // Rollback the transaction in case of any error
  mysqli_rollback($conn);

  $response['message'] = 'Error deleting admin: ' . $e->getMessage();
 }
} else {
 $response['message'] = 'Invalid request method';
}

// Send JSON response
echo json_encode($response);

// Close the database connection
mysqli_close($conn);
exit;
?>