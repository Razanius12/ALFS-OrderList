// unused
<?php
// Include database connection
require 'database.php';

/**
 * Authenticate user
 */
function loginUser($username, $password, $remember = false)
{
 global $conn;  // Use global database connection

 // Trim input
 $username = trim($username);

 // Check workers table first
 $worker_query = "SELECT * FROM workers WHERE username = ?";
 $worker_stmt = $conn->prepare($worker_query);
 $worker_stmt->bind_param("s", $username);
 $worker_stmt->execute();
 $worker_result = $worker_stmt->get_result();

 if ($worker_result->num_rows > 0) {
  $worker = $worker_result->fetch_assoc();

  if ($password === $worker['password']) {
   // Directly set session variables here
   $_SESSION['user_id'] = $worker['id_worker'];
   $_SESSION['username'] = $worker['username'];
   $_SESSION['name'] = $worker['name_worker'];
   $_SESSION['role'] = 'worker';
   $_SESSION['position_id'] = $worker['id_position'];

   // Optional: Handle remember me functionality
   if ($remember) {
    // Your existing remember me logic
    createRememberMeToken($worker['id_worker'], 'worker');
   }

   return true;
  }
 }

 // Check admins table (similar logic)
 $admin_query = "SELECT * FROM admins WHERE username = ?";
 $admin_stmt = $conn->prepare($admin_query);
 $admin_stmt->bind_param("s", $username);
 $admin_stmt->execute();
 $admin_result = $admin_stmt->get_result();

 if ($admin_result->num_rows > 0) {
  $admin = $admin_result->fetch_assoc();

  if ($password === $admin['password']) {
   // Directly set session variables for admin
   $_SESSION['user_id'] = $admin['id_admin'];
   $_SESSION['username'] = $admin['username'];
   $_SESSION['name'] = $admin['name_admin'];
   $_SESSION['role'] = 'admin';
   $_SESSION['position_id'] = $admin['id_position'];

   // Optional: Handle remember me functionality
   if ($remember) {
    createRememberMeToken($admin['id_admin'], 'admin');
   }

   return true;
  }
 }

 // Login failed
 $_SESSION['login_error'] = "Invalid username or password";
 return false;
}

/**
 * Create remember me token
 */
function createRememberMeToken($user_id, $user_type)
{
 global $conn;

 // Generate a unique token
 $token = bin2hex(random_bytes(32));

 // Set expiry (30 days from now)
 $expiry = time() + (30 * 24 * 60 * 60);

 // Prepare and execute insert
 $insert_query = "INSERT INTO remember_tokens (user_id, user_type, token, expiry) VALUES (?, ?, ?, ?)";
 $stmt = $conn->prepare($insert_query);
 $stmt->bind_param("issi", $user_id, $user_type, $token, $expiry);

 // Set cookie
 setcookie('remember_me', $token, $expiry, '/', '', true, true);

 return $stmt->execute();
}

/**
 * Check remember me token
 */
function checkRememberMeToken()
{
 global $conn;

 if (!isset($_COOKIE['remember_me'])) {
  return false;
 }

 $token = $_COOKIE['remember_me'];

 // Verify token in database
 $query = "SELECT * FROM remember_tokens WHERE token = ? AND expiry > ?";
 $stmt = $conn->prepare($query);
 $current_time = time();
 $stmt->bind_param("si", $token, $current_time);
 $stmt->execute();
 $result = $stmt->get_result();

 if ($result->num_rows > 0) {
  $token_data = $result->fetch_assoc();

  // Determine user type and fetch user details
  if ($token_data['user_type'] == 'worker') {
   $user_query = "SELECT * FROM workers WHERE id_worker = ?";
  } else {
   $user_query = "SELECT * FROM admins WHERE id_admin = ?";
  }

  $user_stmt = $conn->prepare($user_query);
  $user_stmt->bind_param("i", $token_data['user_id']);
  $user_stmt->execute();
  $user_result = $user_stmt->get_result();

  if ($user_result->num_rows > 0) {
   $user = $user_result->fetch_assoc();

   // Directly set session variables instead of using createUser Session
   $_SESSION['user_id'] = $token_data['user_id'];
   $_SESSION['username'] = $user['username'];
   $_SESSION['name'] = $token_data['user_type'] == 'worker' ? $user['name_worker'] : $user['name_admin'];
   $_SESSION['role'] = $token_data['user_type'];
   $_SESSION['position_id'] = $user['id_position'];

   return true;
  }
 }

 return false;
}

/**
 * Check if user is logged in
 */
function isLoggedIn()
{
 return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

/**
 * Logout function
 */
function logoutUser()
{
 global $conn;

 // Remove remember me token from database
 if (isset($_COOKIE['remember_me'])) {
  $token = $_COOKIE['remember_me'];
  $delete_query = "DELETE FROM remember_tokens WHERE token = ?";
  $stmt = $conn->prepare($delete_query);
  $stmt->bind_param("s", $token);
  $stmt->execute();

  // Clear remember me cookie
  setcookie('remember_me', '', time() - 3600, '/', '', true, true);
 }

 // Destroy session
 $_SESSION = [];
 session_destroy();
}

// Check remember me if not logged in
if (!isLoggedIn()) {
 checkRememberMeToken();
}