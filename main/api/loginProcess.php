<?php
session_start();

// Debug: Output session status
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../config/database.php'; // Include your database connection

// Get username and password from POST request
$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = mysqli_real_escape_string($conn, $_POST['password']);

// Check for empty fields
if (empty($username) || empty($password)) {
 echo json_encode([
  'status' => 'error',
  'message' => 'Username and password are required.'
 ]);
 exit();
}

// Query for admins
$queryAdmin = mysqli_query($conn, "SELECT * FROM admins WHERE username = '$username' AND password = '$password'");

if (mysqli_num_rows($queryAdmin) == 1) {
 $user = mysqli_fetch_assoc($queryAdmin);

 // Extensive session setting with debugging
 $_SESSION['user_id'] = $user['id_admin'];
 $_SESSION['username'] = $user['username'];
 $_SESSION['level'] = 'admin';
 $_SESSION['name_admin'] = $user['name_admin'];

 // Debug: Verify session is set
 error_log("Admin Session Set: " . print_r($_SESSION, true));

 echo json_encode([
  'status' => 'success',
  'redirect' => '../../index.php',
  'session_data' => $_SESSION // Send session data back for client-side verification
 ]);
 exit();
}

// Query for workers
$queryWorker = mysqli_query($conn, "SELECT * FROM workers WHERE username = '$username' AND password = '$password'");

if (mysqli_num_rows($queryWorker) == 1) {
 $user = mysqli_fetch_assoc($queryWorker);

 // Extensive session setting with debugging
 $_SESSION['user_id'] = $user['id_worker'];
 $_SESSION['username'] = $user['username'];
 $_SESSION['level'] = 'worker';
 $_SESSION['name_worker'] = $user['name_worker'];

 // Debug: Verify session is set
 error_log("Worker Session Set: " . print_r($_SESSION, true));

 echo json_encode([
  'status' => 'success',
  'redirect' => '../../index.php',
  'session_data' => $_SESSION // Send session data back for client-side verification
 ]);
 exit();
}

// If no match found in both tables, return error
echo json_encode([
 'status' => 'error',
 'message' => 'Invalid username or password.'
]);

// Close the database connection
mysqli_close($conn);
?>