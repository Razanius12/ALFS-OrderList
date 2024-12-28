<?php
header('Content-Type: application/json');
require_once '../../config/database.php';

try {
 if (!isset($_POST['task_id'])) {
  throw new Exception('Missing required parameters');
 }

 $task_id = $_POST['task_id'];
 $status = 'COMPLETED'; // Automatically set status to COMPLETED

 // Start transaction
 mysqli_begin_transaction($conn);

 // Create new attachment record
 $stmt = mysqli_prepare($conn, "INSERT INTO attachments (atch1, atch2, atch3, atch4, atch5, atch6, atch7, atch8, atch9, atch10) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

 $attachments = array_fill(0, 10, null); // Initialize array with 10 null values

 if (isset($_FILES['attachments'])) {
  $upload_dir = '../../main/imgdata/orders/';
  if (!file_exists($upload_dir)) {
   mkdir($upload_dir, 0777, true);
  }

  foreach ($_FILES['attachments']['tmp_name'] as $key => $tmp_name) {
   if ($key >= 10)
    break; // Maximum 10 files

   $file = $_FILES['attachments']['name'][$key];
   $size = $_FILES['attachments']['size'][$key];
   $type = $_FILES['attachments']['type'][$key];

   // Validate file size (5MB)
   if ($size > 5 * 1024 * 1024) {
    throw new Exception("File $file exceeds 5MB limit");
   }

   // Generate unique filename
   $ext = pathinfo($file, PATHINFO_EXTENSION);
   $newname = uniqid('order_', true) . '.' . $ext;
   $target = $upload_dir . $newname;

   if (move_uploaded_file($tmp_name, $target)) {
    $attachments[$key] = $newname;
   }
  }
 }

 mysqli_stmt_bind_param(
  $stmt,
  "ssssssssss",
  $attachments[0],
  $attachments[1],
  $attachments[2],
  $attachments[3],
  $attachments[4],
  $attachments[5],
  $attachments[6],
  $attachments[7],
  $attachments[8],
  $attachments[9]
 );

 if (!mysqli_stmt_execute($stmt)) {
  throw new Exception('Failed to save attachments');
 }

 $attach_result_id = mysqli_insert_id($conn);

 // Update order status and attach_result_id
 $update_stmt = mysqli_prepare($conn, "UPDATE orders SET status = ?, finished_at = NOW(), attach_result_id = ? WHERE id_order = ?");
 mysqli_stmt_bind_param($update_stmt, "sii", $status, $attach_result_id, $task_id);

 if (!mysqli_stmt_execute($update_stmt)) {
  throw new Exception('Failed to update order status');
 }

 mysqli_commit($conn);

 echo json_encode([
  'success' => true,
  'message' => 'Task completed and files uploaded successfully'
 ]);

} catch (Exception $e) {
 mysqli_rollback($conn);
 echo json_encode([
  'success' => false,
  'message' => $e->getMessage()
 ]);
}
?>