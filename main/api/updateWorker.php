<?php
// Required headers
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // Replace "*" with the specific domain when deploying
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database connection
require_once '../../config/database.php';

// Initialize response
$response = ["success" => false, "message" => "Invalid input."];

// Only proceed if the request is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
 // Get worker ID and other fields
 $id_worker = $_POST['id_worker'] ?? null;
 $username = $_POST['username'] ?? null;
 $name_worker = $_POST['name_worker'] ?? null;
 $id_position = $_POST['id_position'] ?? null;
 $gender_worker = $_POST['gender_worker'] ?? null;
 $phone_number = $_POST['phone_number'] ?? null;
 $password = $_POST['password'] ?? null; // Password field may be empty if not updating

 // Check if a new position is being created
 if (!empty($_POST['new_position'])) {
  // First, check if the position already exists
  $newPositionName = mysqli_real_escape_string($conn, $_POST['new_position']);
  $checkQuery = "SELECT id_position FROM positions WHERE position_name = '$newPositionName'";
  $checkResult = mysqli_query($conn, $checkQuery);

  if (mysqli_num_rows($checkResult) == 0) {
   // Insert new position
   $insertPositionQuery = "INSERT INTO positions (position_name, department) VALUES ('$newPositionName', 'WORKER')";
   mysqli_query($conn, $insertPositionQuery);

   // Get the ID of the newly inserted position
   $_POST['id_position'] = mysqli_insert_id($conn);
  } else {
   // If position exists, fetch its ID
   $positionRow = mysqli_fetch_assoc($checkResult);
   $_POST['id_position'] = $positionRow['id_position'];
  }
 }

 // Validate required fields
 if ($id_worker && $username && $name_worker && $id_position && $gender_worker && $phone_number) {

  // Prepare SQL query to update worker information
  $query = "UPDATE workers SET 
                   username = ?, 
                   name_worker = ?, 
                   id_position = ?, 
                   gender_worker = ?, 
                   phone_number = ?";

  // Only update password if it is provided
  if (!empty($password)) {
   $query .= ", password = ?";
  }

  $query .= " WHERE id_worker = ?";

  // Prepare and execute statement
  if ($stmt = $conn->prepare($query)) {
   // Bind parameters based on whether password is included
   if (!empty($password)) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt->bind_param("ssisssi", $username, $name_worker, $id_position, $gender_worker, $phone_number, $hashed_password, $id_worker);
   } else {
    $stmt->bind_param("ssissi", $username, $name_worker, $id_position, $gender_worker, $phone_number, $id_worker);
   }

   // Execute and check for success
   if ($stmt->execute()) {
    $response = ["success" => true, "message" => "Worker updated successfully."];
   } else {
    $response["message"] = "Failed to update worker.";
   }

   // Close statement
   $stmt->close();
  } else {
   $response["message"] = "Database error: unable to prepare statement.";
  }
 } else {
  $response["message"] = "Required fields are missing.";
 }
} else {
 $response["message"] = "Invalid request method.";
}

// Close the database connection
$conn->close();

// Return JSON response
echo json_encode($response);
