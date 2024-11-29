<?php
// Required headers
header('Content-Type: application/json');

// Include database connection
require_once '../../config/database.php';

try {
 // Check if order_id is provided
 if (isset($_GET['order_id'])) {
  // Sanitize input
  $order_id = mysqli_real_escape_string($conn, $_GET['order_id']);

  // Prepare comprehensive SQL query
  $query = "SELECT 
                    o.id_order, 
                    o.order_name, 
                    o.description,
                    o.start_date, 
                    o.status,
                    o.project_manager_id,
                    o.worker_id,
                    a.name_admin AS project_manager_name,
                    w.name_worker AS worker_name
                  FROM 
                    orders o
                  LEFT JOIN 
                    admins a ON o.project_manager_id = a.id_admin
                  LEFT JOIN 
                    workers w ON o.worker_id = w.id_worker
                  WHERE 
                    o.id_order = '$order_id'";

  // Execute query
  $result = mysqli_query($conn, $query);

  // Check if query was successful
  if ($result) {
   // Fetch order data
   $order = mysqli_fetch_assoc($result);

   if ($order) {
    // Prepare available workers for dropdown
    $workerQuery = "SELECT 
                                id_worker, 
                                name_worker 
                                FROM workers 
                                WHERE availability_status = 'AVAILABLE'";
    $workerResult = mysqli_query($conn, $workerQuery);

    $workerOptions = [];
    while ($worker = mysqli_fetch_assoc($workerResult)) {
     $workerOptions[] = $worker;
    }

    // Return order data as JSON
    echo json_encode([
     'success' => true,
     'data' => $order,
     'workers' => $workerOptions
    ]);
   } else {
    // Order not found
    echo json_encode([
     'success' => false,
     'message' => 'Order not found'
    ]);
   }

   // Free result sets
   mysqli_free_result($result);
   if (isset($workerResult)) {
    mysqli_free_result($workerResult);
   }
  } else {
   // Query failed
   echo json_encode([
    'success' => false,
    'message' => 'Error executing query: ' . mysqli_error($conn)
   ]);
  }
 } else {
  // No order ID provided
  echo json_encode([
   'success' => false,
   'message' => 'Order ID is required'
  ]);
 }
} catch (Exception $e) {
 echo json_encode([
  'success' => false,
  'message' => $e->getMessage()
 ]);
}
?>