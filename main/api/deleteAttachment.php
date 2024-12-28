<?php
header('Content-Type: application/json');
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
 http_response_code(401);
 echo json_encode(['success' => false, 'message' => 'Unauthorized']);
 exit;
}

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

$data = json_decode(file_get_contents('php://input'), true);
$orderId = $data['order_id'] ?? null;
$attachmentColumn = $data['attachment_column'] ?? null;
$deleteAll = $data['delete_all'] ?? false;
$type = $data['type'] ?? 'all';

try {
 mysqli_begin_transaction($conn);

 if ($deleteAll) {
  $columnToCheck = ($type === 'reference' || $type === 'all') ? 'references_id' : '';
  $columnToCheck2 = ($type === 'attachment' || $type === 'all') ? 'attach_result_id' : '';

  $query = "SELECT * FROM orders WHERE id_order = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('i', $orderId);
  $stmt->execute();
  $order = $stmt->get_result()->fetch_assoc();

  if ($type === 'reference' || $type === 'all') {
   if ($order['references_id']) {
    // Delete reference files
    $query = "SELECT * FROM attachments WHERE id_attachment = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $order['references_id']);
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
    $stmt->bind_param('i', $order['references_id']);
    $stmt->execute();

    // Update order
    $updateQuery = "UPDATE orders SET references_id = NULL WHERE id_order = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param('i', $orderId);
    $stmt->execute();
   }
  }

  if ($type === 'attachment' || $type === 'all') {
   if ($order['attach_result_id']) {
    // Delete attachment files
    $query = "SELECT * FROM attachments WHERE id_attachment = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $order['attach_result_id']);
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
    $stmt->bind_param('i', $order['attach_result_id']);
    $stmt->execute();

    // Update order
    $updateQuery = "UPDATE orders SET attach_result_id = NULL WHERE id_order = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param('i', $orderId);
    $stmt->execute();
   }
  }
 } else {
  // Individual file deletion
  if (!$attachmentColumn) {
   throw new Exception('Missing attachment column identifier');
  }

  $columnToCheck = $type === 'reference' ? 'references_id' : 'attach_result_id';

  $query = "SELECT o.*, a.* FROM orders o 
                 LEFT JOIN attachments a ON a.id_attachment = o.$columnToCheck 
                 WHERE o.id_order = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('i', $orderId);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();

  if ($row[$attachmentColumn]) {
   // Delete file
   deleteFile($row[$attachmentColumn]);

   // Update attachment record
   $updateQuery = "UPDATE attachments SET $attachmentColumn = NULL 
                          WHERE id_attachment = ?";
   $stmt = $conn->prepare($updateQuery);
   $stmt->bind_param('i', $row['id_attachment']);
   $stmt->execute();

   // Check if all columns are empty
   $checkQuery = "SELECT * FROM attachments WHERE id_attachment = ? AND 
                         (atch1 IS NOT NULL OR atch2 IS NOT NULL OR atch3 IS NOT NULL OR 
                          atch4 IS NOT NULL OR atch5 IS NOT NULL OR atch6 IS NOT NULL OR 
                          atch7 IS NOT NULL OR atch8 IS NOT NULL OR atch9 IS NOT NULL OR 
                          atch10 IS NOT NULL)";
   $stmt = $conn->prepare($checkQuery);
   $stmt->bind_param('i', $row['id_attachment']);
   $stmt->execute();
   $result = $stmt->get_result();

   if ($result->num_rows === 0) {
    // Delete attachment record and update order
    $deleteQuery = "DELETE FROM attachments WHERE id_attachment = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param('i', $row['id_attachment']);
    $stmt->execute();

    $updateQuery = "UPDATE orders SET $columnToCheck = NULL WHERE id_order = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param('i', $orderId);
    $stmt->execute();
   }
  }
 }

 mysqli_commit($conn);
 echo json_encode([
  'success' => true,
  'message' => $deleteAll ? 'Files deleted successfully' : 'File deleted successfully'
 ]);

} catch (Exception $e) {
 mysqli_rollback($conn);
 http_response_code(500);
 echo json_encode([
  'success' => false,
  'message' => $e->getMessage()
 ]);
}

$conn->close();
?>