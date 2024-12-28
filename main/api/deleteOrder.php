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

function deleteFile($fileName)
{
 if ($fileName) {
  $filePath = '../../main/imgdata/orders/' . $fileName;
  if (file_exists($filePath)) {
   return unlink($filePath);
  }
 }
 return true;
}

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
 $check_query = "SELECT id_order, order_name, references_id, attach_result_id FROM orders WHERE id_order = ?";
 $stmt = mysqli_prepare($conn, $check_query);
 mysqli_stmt_bind_param($stmt, "i", $order_id);
 mysqli_stmt_execute($stmt);
 mysqli_stmt_store_result($stmt);

 if (mysqli_stmt_num_rows($stmt) === 0) {
  throw new Exception('Order not found');
 }

 // Fetch order details for logging and further processing
 mysqli_stmt_bind_result($stmt, $id, $order_name, $references_id, $attach_result_id);
 mysqli_stmt_fetch($stmt);
 mysqli_stmt_close($stmt);

 // Begin transaction
 mysqli_begin_transaction($conn);

 // Nullify references_id and attach_result_id in orders table
 $update_query = "UPDATE orders SET references_id = NULL, attach_result_id = NULL WHERE id_order = ?";
 $stmt = mysqli_prepare($conn, $update_query);
 mysqli_stmt_bind_param($stmt, "i", $order_id);
 mysqli_stmt_execute($stmt);
 mysqli_stmt_close($stmt);

 // Delete reference files and attachment records
 if ($references_id) {
  $query = "SELECT * FROM attachments WHERE id_attachment = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('i', $references_id);
  $stmt->execute();
  $attachment = $stmt->get_result()->fetch_assoc();

  for ($i = 1; $i <= 10; $i++) {
   $column = "atch" . $i;
   if ($attachment[$column]) {
    deleteFile($attachment[$column]);
   }
  }

  // Delete reference record
  $deleteQuery = "DELETE FROM attachments WHERE id_attachment = ?";
  $stmt = $conn->prepare($deleteQuery);
  $stmt->bind_param('i', $references_id);
  $stmt->execute();
 }

 if ($attach_result_id) {
  $query = "SELECT * FROM attachments WHERE id_attachment = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('i', $attach_result_id);
  $stmt->execute();
  $attachment = $stmt->get_result()->fetch_assoc();

  for ($i = 1; $i <= 10; $i++) {
   $column = "atch" . $i;
   if ($attachment[$column]) {
    deleteFile($attachment[$column]);
   }
  }

  // Delete attachment record
  $deleteQuery = "DELETE FROM attachments WHERE id_attachment = ?";
  $stmt = $conn->prepare($deleteQuery);
  $stmt->bind_param('i', $attach_result_id);
  $stmt->execute();
 }

 // Prepare delete query for order
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

 // Commit transaction
 mysqli_commit($conn);

} catch (Exception $e) {
 // Rollback transaction in case of error
 mysqli_rollback($conn);
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