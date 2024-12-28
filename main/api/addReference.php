<?php
// Required headers
header('Content-Type: application/json');

// Database connection
require_once '../../config/database.php';

// Initialize response array
$response = [
 'success' => false,
 'message' => '',
 'data' => null
];

try {
 // Check if request method is POST
 if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  throw new Exception('Invalid request method');
 }

 // Validate required fields
 if (!isset($_POST['order_id']) || empty($_POST['order_id'])) {
  throw new Exception('order_id is required');
 }

 if (!isset($_FILES['reference']) || empty($_FILES['reference']['name'])) {
  throw new Exception('Reference file is required');
 }

 // Start transaction
 mysqli_begin_transaction($conn);

 // Handle file upload for reference
 $order_id = (int) $_POST['order_id'];
 $upload_dir = '../../main/imgdata/orders/';
 if (!file_exists($upload_dir)) {
  mkdir($upload_dir, 0777, true);
 }

 $file = $_FILES['reference']['name'];
 $size = $_FILES['reference']['size'];
 $type = $_FILES['reference']['type'];
 $tmp_name = $_FILES['reference']['tmp_name'];

 // Validate file size (5MB)
 if ($size > 5 * 1024 * 1024) {
  throw new Exception("File $file exceeds 5MB limit");
 }

 // Generate unique filename
 $ext = pathinfo($file, PATHINFO_EXTENSION);
 $newname = uniqid('ref_', true) . '.' . $ext;
 $target = $upload_dir . $newname;

 if (!move_uploaded_file($tmp_name, $target)) {
  throw new Exception('Failed to upload reference file');
 }

 // Get the current references_id from the order
 $query = "SELECT references_id FROM orders WHERE id_order = ?";
 $stmt = mysqli_prepare($conn, $query);
 mysqli_stmt_bind_param($stmt, "i", $order_id);
 mysqli_stmt_execute($stmt);
 mysqli_stmt_bind_result($stmt, $references_id);
 mysqli_stmt_fetch($stmt);
 mysqli_stmt_close($stmt);

 if (!$references_id) {
  throw new Exception('No references found for this order');
 }

 // Fetch the current attachments
 $query = "SELECT * FROM attachments WHERE id_attachment = ?";
 $stmt = mysqli_prepare($conn, $query);
 mysqli_stmt_bind_param($stmt, "i", $references_id);
 mysqli_stmt_execute($stmt);
 $result = mysqli_stmt_get_result($stmt);
 $attachments = mysqli_fetch_assoc($result);
 mysqli_stmt_close($stmt);

 // Find the next available attachment slot
 $attachment_column = null;
 for ($i = 1; $i <= 10; $i++) {
  if (empty($attachments["atch$i"])) {
   $attachment_column = "atch$i";
   break;
  }
 }

 if (!$attachment_column) {
  throw new Exception('Maximum number of attachments reached');
 }

 // Update the attachments table with the new reference
 $query = "UPDATE attachments SET $attachment_column = ? WHERE id_attachment = ?";
 $stmt = mysqli_prepare($conn, $query);
 mysqli_stmt_bind_param($stmt, "si", $newname, $references_id);

 if (!mysqli_stmt_execute($stmt)) {
  throw new Exception('Failed to update reference attachment');
 }

 mysqli_stmt_close($stmt);

 // Commit transaction
 mysqli_commit($conn);

 $response['success'] = true;
 $response['message'] = 'Reference added successfully';
 $response['data'] = [
  'order_id' => $order_id,
  'reference' => $newname
 ];

} catch (Exception $e) {
 // Rollback transaction on error
 mysqli_rollback($conn);

 $response['success'] = false;
 $response['message'] = $e->getMessage();
 error_log("Exception: " . $e->getMessage());
 error_log("Trace: " . $e->getTraceAsString());
} finally {
 mysqli_close($conn);
 echo json_encode($response, JSON_PRETTY_PRINT);
 exit;
}