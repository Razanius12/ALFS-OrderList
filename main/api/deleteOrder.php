<?php
// Required headers
header('Content-Type: application/json');

// Database connection
require_once '../../config/database.php';

// Initialize response array
$response = [
 'success' => false,
 'message' => ''
];

try {
 // Check if request method is POST
 if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  throw new Exception('Invalid request method');
 }

 // Validate order ID
 if (!isset($_POST['id_order']) || empty($_POST['id_order'])) {
  throw new Exception('Order ID is required');
 }

 // Sanitize order ID
 $order_id = (int) $_POST['id_order'];

 // Check if order exists before deletion
 $check_query = "SELECT id_order, order_name FROM orders WHERE id_order = ?";
 $stmt = mysqli_prepare($conn, $check_query);
 mysqli_stmt_bind_param($stmt, "i", $order_id);
 mysqli_stmt_execute($stmt);
 mysqli_stmt_store_result($stmt);

 if (mysqli_stmt_num_rows($stmt) === 0) {
  throw new Exception('Order not found');
 }

 // Fetch order name for logging
 mysqli_stmt_bind_result($stmt, $id, $order_name);
 mysqli_stmt_fetch($stmt);
 mysqli_stmt_close($stmt);

 // Prepare delete query
 $delete_query = "DELETE FROM orders WHERE id_order = ?";
 $stmt = mysqli_prepare($conn, $delete_query);
 mysqli_stmt_bind_param($stmt, "i", $order_id);

 if (mysqli_stmt_execute($stmt)) {
  // Check if any rows were actually deleted
  if (mysqli_stmt_affected_rows($stmt) > 0) {
   $response['success'] = true;
   $response['message'] = "Order '{$order_name}' deleted successfully";
  } else {
   throw new Exception('No order was deleted');
  }
 } else {
  throw new Exception('Failed to delete order: ' . mysqli_error($conn));
 }

 mysqli_stmt_close($stmt);

} catch (Exception $e) {
 $response['success'] = false;
 $response['message'] = $e->getMessage();

 // Debug: Log the full exception
 error_log("Exception in deleteOrder: " . $e->getMessage());
 error_log("Trace: " . $e->getTraceAsString());
} finally {
 mysqli_close($conn);

 // Ensure JSON is properly encoded
 echo json_encode($response);
 exit;
}
?>