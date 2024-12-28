<?php
header('Content-Type: application/json');
require_once '../../config/database.php';

try {
 if (!isset($_GET['order_id'])) {
  throw new Exception('Order ID is required');
 }

 $order_id = $_GET['order_id'];

 $query = "SELECT 
        o.order_name, 
        o.description,
        a.id_attachment as result_id,
        r.id_attachment as ref_id,
        " . implode(', ', array_map(function ($i) {
  return "a.atch$i as result_atch$i, r.atch$i as ref_atch$i";
 }, range(1, 10))) . "
    FROM orders o 
    LEFT JOIN attachments a ON o.attach_result_id = a.id_attachment 
    LEFT JOIN attachments r ON o.references_id = r.id_attachment 
    WHERE o.id_order = ?";

 $stmt = mysqli_prepare($conn, $query);
 mysqli_stmt_bind_param($stmt, "i", $order_id);
 mysqli_stmt_execute($stmt);
 $result = mysqli_stmt_get_result($stmt);

 if ($row = mysqli_fetch_assoc($result)) {
  $attachments = [];
  $references = [];

  // Process result attachments
  for ($i = 1; $i <= 10; $i++) {
   $field = "result_atch$i";
   if (!empty($row[$field])) {
    $attachments[] = [
     'name' => $row[$field],
     'path' => 'main/imgdata/orders/' . $row[$field]
    ];
   }
  }

  // Process reference attachments
  for ($i = 1; $i <= 10; $i++) {
   $field = "ref_atch$i";
   if (!empty($row[$field])) {
    $references[] = [
     'name' => $row[$field],
     'path' => 'main/imgdata/orders/' . $row[$field]
    ];
   }
  }

  echo json_encode([
   'success' => true,
   'order_name' => $row['order_name'],
   'description' => $row['description'],
   'attachments' => $attachments,
   'references' => $references
  ]);
 } else {
  throw new Exception('Order not found');
 }

} catch (Exception $e) {
 echo json_encode([
  'success' => false,
  'message' => $e->getMessage()
 ]);
}
?>