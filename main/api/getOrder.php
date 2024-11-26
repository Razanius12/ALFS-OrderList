<?php
// Required headers
header('Content-Type: application/json');

// Include database connection
require_once '../../config/database.php';

if (isset($_GET['id_order'])) {
 $id_order = $_GET['id_order'];

 $query = "SELECT o.*, 
              pa.id_worker,
              w.name_worker
              FROM orders o
              LEFT JOIN project_assignments pa ON o.id_order = pa.id_order 
                AND pa.status != 'COMPLETED'
              LEFT JOIN workers w ON pa.id_worker = w.id_worker
              WHERE o.id_order = ?";

 $stmt = mysqli_prepare($conn, $query);
 mysqli_stmt_bind_param($stmt, "i", $id_order);
 mysqli_stmt_execute($stmt);
 $result = mysqli_stmt_get_result($stmt);

 if ($row = mysqli_fetch_assoc($result)) {
  echo json_encode($row);
 } else {
  echo json_encode(['error' => 'Order not found']);
 }
} else {
 echo json_encode(['error' => 'No order ID provided']);
}