<?php
// Required headers
header('Content-Type: application/json');

// Database connection
require_once '../../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
 $position_name = trim($_POST['position_name']);
 $department = $_POST['department'];

 // Validate input
 $errors = [];

 if (empty($position_name)) {
  $errors[] = "Position name is required";
 } elseif (strlen($position_name) > 32) {
  $errors[] = "Position name must be less than 32 characters";
 }

 if (empty($department)) {
  $errors[] = "Department is required";
 } elseif (!in_array($department, ['ADMIN', 'WORKER'])) {
  $errors[] = "Invalid department selected";
 }

 if (empty($errors)) {
  // Prepare SQL to insert new position
  $sql = "INSERT INTO positions (position_name, department) VALUES (?, ?)";

  try {
   $stmt = $conn->prepare($sql);
   $stmt->bind_param("ss", $position_name, $department);

   if ($stmt->execute()) {
    echo json_encode([
     'success' => true,
     'message' => 'New position added successfully!',
     'id' => $stmt->insert_id
    ]);
   } else {
    echo json_encode([
     'success' => false,
     'message' => 'Error adding position: ' . $stmt->error
    ]);
   }

   $stmt->close();
  } catch (Exception $e) {
   echo json_encode([
    'success' => false,
    'message' => 'Database error: ' . $e->getMessage()
   ]);
  }
 } else {
  echo json_encode([
   'success' => false,
   'message' => $errors
  ]);
 }
} else {
 echo json_encode([
  'success' => false,
  'message' => 'Invalid request method'
 ]);
}
?>