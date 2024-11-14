<?php
// Required headers
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *"); // the "*" will later be replaced with the domain of the website
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Database connection
require_once '../../config/database.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
 // Validate inputs
 $positionName = trim($_POST['positionName'] ?? '');
 $department = $_POST['department'] ?? '';

 // Input validation
 $errors = [];

 if (empty($positionName)) {
  $errors[] = "Position name is required.";
 }

 if (!in_array($department, ['WORKER', 'ADMIN'])) {
  $errors[] = "Invalid department selected.";
 }

 // Check if position already exists
 $checkQuery = "SELECT idPosition FROM positions 
                WHERE positionName = ? AND department = ?";
 $checkStmt = $conn->prepare($checkQuery);
 $checkStmt->bind_param("ss", $positionName, $department);
 $checkStmt->execute();
 $checkResult = $checkStmt->get_result();

 if ($checkResult->num_rows > 0) {
  $errors[] = "Position already exists.";
 }

 // If no errors, proceed with insertion
 if (empty($errors)) {
  $insertQuery = "INSERT INTO positions (positionName, department) VALUES (?, ?)";
  $insertStmt = $conn->prepare($insertQuery);
  $insertStmt->bind_param("ss", $positionName, $department);

  if ($insertStmt->execute()) {
   // Set success message in session
   $_SESSION['successMessage'] = "Position '$positionName' added successfully!";

   // Redirect back to workers.php
   header("Location: workers.php");
   exit();
  } else {
   $errors[] = "Failed to add position. Please try again.";
  }
 }

 // If there are errors, store them in session
 if (!empty($errors)) {
  $_SESSION['errorMessages'] = $errors;
  header("Location: main/pages/addNewPosition.php");
  exit();
 }
}