<?php
session_start();

require_once '../../config/database.php';
require_once '../../config/session.php'; // Include the session functions

// Get username and password from POST request
$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = mysqli_real_escape_string($conn, $_POST['password']);

// Check for remember me
$remember_me = isset($_POST['remember_me']) && $_POST['remember_me'] == '1';

// Check for empty fields
if (empty($username) || empty($password)) {
 echo json_encode([
  'status' => 'error',
  'message' => 'Username and password are required.'
 ]);
 exit();
}

// Function to handle successful login
function handleSuccessfulLogin($conn, $user, $user_type, $remember_me = false)
{
 // Clear any existing session data
 $_SESSION = array();

 // Set new session variables
 $_SESSION['user_id'] = $user_type == 'admin' ? $user['id_admin'] : $user['id_worker'];
 $_SESSION['username'] = $user['username'];
 $_SESSION['level'] = $user_type;

 // Set name based on user type with explicit naming
 if ($user_type == 'admin') {
  $_SESSION['name_admin'] = $user['name_admin'];
  $_SESSION['name'] = $user['name_admin'];
  $_SESSION['profile_pic'] = $user['profile_pic'];
 } else {
  $_SESSION['name_worker'] = $user['name_worker'];
  $_SESSION['name'] = $user['name_worker'];
  $_SESSION['profile_pic'] = $user['profile_pic'];
 }

 // Debug logging
 error_log("Login Session Set: " . print_r($_SESSION, true));

 // Handle remember me functionality
 if ($remember_me) {
  createRememberMeToken(
   $_SESSION['user_id'],
   $user_type
  );
 }

 // Return login success response
 return [
  'status' => 'success',
  'redirect' => '../../index.php',
  'session_data' => $_SESSION
 ];
}

// Improved authentication function
function authenticateUser($conn, $username, $password, $table, $user_type)
{
 // Use prepared statement to prevent SQL injection
 $stmt = $conn->prepare("SELECT * FROM $table WHERE username = ? AND password = ?");
 $stmt->bind_param("ss", $username, $password);
 $stmt->execute();
 $result = $stmt->get_result();

 if ($result->num_rows == 1) {
  $user = $result->fetch_assoc();

  // Debug logging
  error_log("$user_type User Found: " . print_r($user, true));

  return $user;
 }

 return null;
}

// Try to authenticate as admin
$adminUser = authenticateUser($conn, $username, $password, 'admins', 'Admin');
if ($adminUser) {
 $response = handleSuccessfulLogin($conn, $adminUser, 'admin', $remember_me);
 echo json_encode($response);
 exit();
}

// Try to authenticate as worker
$workerUser = authenticateUser($conn, $username, $password, 'workers', 'Worker');
if ($workerUser) {
 $response = handleSuccessfulLogin($conn, $workerUser, 'worker', $remember_me);
 echo json_encode($response);
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