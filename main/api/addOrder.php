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
 $required_fields = ['order_name', 'project_manager_id', 'start_date', 'deadline'];
 foreach ($required_fields as $field) {
  if (!isset($_POST[$field]) || empty($_POST[$field])) {
   throw new Exception("$field is required");
  }
 }

 // Start transaction
 mysqli_begin_transaction($conn);

 // Handle file uploads for references if present
 $references_id = null;
 if (isset($_FILES['references']) && !empty($_FILES['references']['name'][0])) {
  // Create new attachment record for references
  $stmt = mysqli_prepare($conn, "INSERT INTO attachments (atch1, atch2, atch3, atch4, atch5, atch6, atch7, atch8, atch9, atch10) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

  $attachments = array_fill(0, 10, null); // Initialize array with 10 null values

  $upload_dir = '../../main/imgdata/orders/';
  if (!file_exists($upload_dir)) {
   mkdir($upload_dir, 0777, true);
  }

  foreach ($_FILES['references']['tmp_name'] as $key => $tmp_name) {
   if ($key >= 10)
    break; // Maximum 10 files

   $file = $_FILES['references']['name'][$key];
   $size = $_FILES['references']['size'][$key];
   $type = $_FILES['references']['type'][$key];

   // Validate file size (5MB)
   if ($size > 5 * 1024 * 1024) {
    throw new Exception("File $file exceeds 5MB limit");
   }

   // Generate unique filename
   $ext = pathinfo($file, PATHINFO_EXTENSION);
   $newname = uniqid('ref_', true) . '.' . $ext;
   $target = $upload_dir . $newname;

   if (move_uploaded_file($tmp_name, $target)) {
    $attachments[$key] = $newname;
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
   throw new Exception('Failed to save reference attachments');
  }

  $references_id = mysqli_insert_id($conn);
  mysqli_stmt_close($stmt);
 }

 // Sanitize and validate input
 $order_name = mysqli_real_escape_string($conn, $_POST['order_name']);
 $project_manager_id = (int) $_POST['project_manager_id'];
 $start_date = date('Y-m-d H:i:s', strtotime($_POST['start_date']));
 $deadline = date('Y-m-d', strtotime($_POST['deadline']));
 $worker_id = isset($_POST['assigned_worker']) && !empty($_POST['assigned_worker']) ? (int) $_POST['assigned_worker'] : NULL;
 $description = isset($_POST['description']) ? mysqli_real_escape_string($conn, $_POST['description']) : NULL;
 $order_price = isset($_POST['order_price']) ? (int) $_POST['order_price'] : NULL;

 // Validate project manager ID
 $check_manager_query = "SELECT id_admin FROM admins WHERE id_admin = ?";
 $stmt = mysqli_prepare($conn, $check_manager_query);
 mysqli_stmt_bind_param($stmt, "i", $project_manager_id);
 mysqli_stmt_execute($stmt);
 mysqli_stmt_store_result($stmt);

 if (mysqli_stmt_num_rows($stmt) === 0) {
  throw new Exception('Invalid project manager selected');
 }
 mysqli_stmt_close($stmt);

 // Validate worker ID if provided
 if ($worker_id !== NULL) {
  $check_worker_query = "SELECT id_worker FROM workers WHERE id_worker = ?";
  $stmt = mysqli_prepare($conn, $check_worker_query);
  mysqli_stmt_bind_param($stmt, "i", $worker_id);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_store_result($stmt);

  if (mysqli_stmt_num_rows($stmt) === 0) {
   throw new Exception('Invalid or unavailable worker selected');
  }
  mysqli_stmt_close($stmt);
 }

 // Insert new order query
 $insert_query = "INSERT INTO orders (
        order_name, 
        start_date, 
        deadline, 
        project_manager_id, 
        worker_id,
        description,
        status,
        order_price,
        references_id
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

 $default_status = 'PENDING';

 $stmt = mysqli_prepare($conn, $insert_query);
 mysqli_stmt_bind_param(
  $stmt,
  "sssiissii",
  $order_name,
  $start_date,
  $deadline,
  $project_manager_id,
  $worker_id,
  $description,
  $default_status,
  $order_price,
  $references_id
 );

 if (!mysqli_stmt_execute($stmt)) {
  throw new Exception('Failed to add order: ' . mysqli_error($conn));
 }

 $order_id = mysqli_insert_id($conn);
 mysqli_stmt_close($stmt);

 // Commit transaction
 mysqli_commit($conn);

 $response['success'] = true;
 $response['message'] = 'Order added successfully';
 $response['data'] = [
  'id_order' => $order_id,
  'order_name' => $order_name,
  'start_date' => $start_date,
  'deadline' => $deadline,
  'worker_assigned' => $worker_id ? 'Yes' : 'No',
  'has_references' => $references_id ? true : false
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