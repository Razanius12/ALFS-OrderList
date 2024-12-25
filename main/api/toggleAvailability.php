<?php
// Required headers
header('Content-Type: application/json');

// Database connection
require_once '../../config/database.php';
session_start();

// Check if user is logged in and is a worker
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'worker') {
 echo json_encode([
  'success' => false,
  'message' => 'Unauthorized access'
 ]);
 exit;
}

try {
 // Get current worker's status
 $workerId = $_SESSION['user_id'];
 $statusQuery = "SELECT availability_status, assigned_order_id FROM workers WHERE id_worker = ?";
 $stmt = mysqli_prepare($conn, $statusQuery);
 mysqli_stmt_bind_param($stmt, "i", $workerId);
 mysqli_stmt_execute($stmt);
 $result = mysqli_stmt_get_result($stmt);
 $worker = mysqli_fetch_assoc($result);

 // Toggle the status
 $newStatus = $worker['availability_status'] === 'AVAILABLE' ? 'TASKED' : 'AVAILABLE';

 $updateQuery = "UPDATE workers SET availability_status = ? WHERE id_worker = ?";
 $stmt = mysqli_prepare($conn, $updateQuery);
 mysqli_stmt_bind_param($stmt, "si", $newStatus, $workerId);

 if (mysqli_stmt_execute($stmt)) {
  echo json_encode([
   'success' => true,
   'new_status' => $newStatus
  ]);
 } else {
  echo json_encode([
   'success' => false,
   'message' => 'Database error occurred'
  ]);
 }

} catch (Exception $e) {
 error_log("Toggle Availability Error: " . $e->getMessage());
 echo json_encode([
  'success' => false,
  'message' => 'An error occurred'
 ]);
}

mysqli_close($conn);