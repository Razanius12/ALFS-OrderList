<?php
// Required headers
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // the "*" will later be replaced with the domain of the website
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Database connection
require_once '../../config/database.php';

// Function to validate input
function validateInput($data)
{
 $errors = [];

 if (empty($data['order_name'])) {
  $errors[] = "Project name is required";
 }

 if (empty($data['project_manager_id'])) {
  $errors[] = "Project manager is required";
 }

 if (empty($data['start_date'])) {
  $errors[] = "Start date is required";
 } else {
  // Validate date format
  $date = date_create($data['start_date']);
  if (!$date) {
   $errors[] = "Invalid date format";
  }
 }

 if (empty($data['status']) || !in_array($data['status'], ['PENDING', 'IN_PROGRESS', 'COMPLETED', 'CANCELLED'])) {
  $errors[] = "Valid status is required";
 }

 // Validate project manager exists and is a manager
 if (!empty($data['project_manager_id'])) {
  global $conn;
  $stmt = $conn->prepare("
            SELECT a.id_admin 
            FROM admins a
            JOIN positions p ON a.id_position = p.id_position
            WHERE a.id_admin = ? 
            AND p.position_name LIKE '%manager%'
            AND p.department = 'ADMIN'
        ");
  $stmt->bind_param("i", $data['project_manager_id']);
  $stmt->execute();
  if (!$stmt->get_result()->fetch_assoc()) {
   $errors[] = "Invalid project manager selected";
  }
  $stmt->close();
 }

 return $errors;
}

try {
 // Check if it's a POST request
 if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  throw new Exception('Only POST method is allowed');
 }

 // Get posted data
 $data = [
  'order_name' => trim($_POST['order_name'] ?? ''),
  'project_manager_id' => trim($_POST['project_manager_id'] ?? ''),
  'start_date' => trim($_POST['start_date'] ?? ''),
  'status' => trim($_POST['status'] ?? ''),
  'description' => trim($_POST['description'] ?? ''),
  'worker_ids' => isset($_POST['worker_ids']) ? (array) $_POST['worker_ids'] : []
 ];

 // Begin transaction
 $conn->begin_transaction();

 // Validate input
 $errors = validateInput($data);
 if (!empty($errors)) {
  throw new Exception('Validation failed: ' . implode(', ', $errors));
 }

 // Insert order
 $stmt = $conn->prepare("
        INSERT INTO orders (
            order_name, 
            project_manager_id, 
            start_date, 
            status, 
            description,
            created_at
        ) VALUES (?, ?, ?, ?, ?, NOW())
    ");

 $stmt->bind_param(
  "sssss",
  $data['order_name'],
  $data['project_manager_id'],
  $data['start_date'],
  $data['status'],
  $data['description']
 );

 if (!$stmt->execute()) {
  throw new Exception("Error creating order: " . $stmt->error);
 }

 $order_id = $conn->insert_id;
 $stmt->close();

 // Insert worker assignments if any
 if (!empty($data['worker_ids'])) {
  $stmt = $conn->prepare("
            INSERT INTO project_assignments (
                id_order, 
                id_worker, 
                id_admin, 
                assigned_at
            ) VALUES (?, ?, ?, NOW())
        ");

  foreach ($data['worker_ids'] as $worker_id) {
   $stmt->bind_param(
    "iii",
    $order_id,
    $worker_id,
    $data['project_manager_id']  // Using the same project manager for assignments
   );
   if (!$stmt->execute()) {
    throw new Exception("Error assigning worker: " . $stmt->error);
   }
  }
  $stmt->close();
 }

 // Commit transaction
 $conn->commit();

 // Fetch the created order with assignments
 $query = "SELECT 
                o.id_order, 
                o.order_name, 
                o.status, 
                o.start_date, 
                a.name_admin as project_manager,
                GROUP_CONCAT(DISTINCT w.name_worker) as assigned_workers
              FROM orders o
              LEFT JOIN admins a ON o.project_manager_id = a.id_admin
              LEFT JOIN project_assignments pa ON o.id_order = pa.id_order
              LEFT JOIN workers w ON pa.id_worker = w.id_worker
              WHERE o.id_order = ?
              GROUP BY o.id_order";

 $stmt = $conn->prepare($query);
 $stmt->bind_param("i", $order_id);
 $stmt->execute();
 $result = $stmt->get_result();
 $order_data = $result->fetch_assoc();

 // Create success response
 http_response_code(201);
 echo json_encode([
  'success' => true,
  'message' => 'Order created successfully',
  'data' => $order_data
 ]);

} catch (Exception $e) {
 // Rollback transaction on error
 if ($conn->connect_error === null) {
  $conn->rollback();
 }

 // Handle any errors
 http_response_code(500);
 echo json_encode([
  'success' => false,
  'message' => 'Failed to create order',
  'error' => $e->getMessage()
 ]);
}

// Close connection
$conn->close();