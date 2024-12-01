<?php
// Include database connection
require_once 'database.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
 session_start();
}

class Authentication
{
 private $conn;
 private $remember_cookie_name = 'auth_remember';
 private $cookie_expiry = 30 * 24 * 60 * 60; // 30 days

 public function __construct($database_connection)
 {
  $this->conn = $database_connection;
 }

 // Login method with remember me option
 public function login($username, $password, $remember = false)
 {
  // First, check in workers table
  $worker_query = "SELECT * FROM workers WHERE username = ?";
  $worker_stmt = $this->conn->prepare($worker_query);
  $worker_stmt->bind_param("s", $username);
  $worker_stmt->execute();
  $worker_result = $worker_stmt->get_result();

  if ($worker_result->num_rows > 0) {
   $worker = $worker_result->fetch_assoc();

   // Verify worker password
   if ($password === $worker['password']) {
    $this->setLoginSession($worker['id_worker'], $worker['username'], $worker['name_worker'], 'worker', $worker['id_position']);

    if ($remember) {
     $this->createRememberMeCookie($worker['id_worker'], 'worker');
    }

    return true;
   }
  }

  // If not a worker, check in admins table
  $admin_query = "SELECT * FROM admins WHERE username = ?";
  $admin_stmt = $this->conn->prepare($admin_query);
  $admin_stmt->bind_param("s", $username);
  $admin_stmt->execute();
  $admin_result = $admin_stmt->get_result();

  if ($admin_result->num_rows > 0) {
   $admin = $admin_result->fetch_assoc();

   // Verify admin password
   if ($password === $admin['password']) {
    $this->setLoginSession($admin['id_admin'], $admin['username'], $admin['name_admin'], 'admin', $admin['id_position']);

    if ($remember) {
     $this->createRememberMeCookie($admin['id_admin'], 'admin');
    }

    return true;
   }
  }

  // Login failed - no worker or admin found
  return false;

  // When login is successful, you can optionally create a remember me token
  if ($remember) {
   $this->createRememberMeCookie($user_id, $user_type);
  }

  return $loginSuccess;
 }

 // Set login session
 private function setLoginSession($user_id, $username, $name, $role, $position_id)
 {
  $_SESSION['user_id'] = $user_id;
  $_SESSION['username'] = $username;
  $_SESSION['name'] = $name;
  $_SESSION['role'] = $role;
  $_SESSION['position_id'] = $position_id;
 }

 // Create remember me cookie
 private function createRememberMeCookie($user_id, $user_type)
 {
  // Generate unique token
  $token = bin2hex(random_bytes(32));
  $expiry = time() + $this->cookie_expiry;

  // Store token in database
  $query = "INSERT INTO remember_tokens (user_id, user_type, token, expiry) VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE token = ?, expiry = ?";
  $stmt = $this->conn->prepare($query);
  $stmt->bind_param(
   "isssis",
   $user_id,
   $user_type,
   $token,
   $expiry,
   $token,
   $expiry
  );
  $stmt->execute();

  // Set secure HTTP-only cookie
  setcookie(
   $this->remember_cookie_name,
   $token,
   [
    'expires' => $expiry,
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
   ]
  );
 }

 // Check remember me cookie
 public function checkRememberMeCookie()
 {
  // Check if user is already logged in
  if ($this->isLoggedIn()) {
   return true;
  }

  // Check if remember me cookie exists
  if (!isset($_COOKIE[$this->remember_cookie_name])) {
   return false;
  }

  $token = $_COOKIE[$this->remember_cookie_name];

  // Verify token in database
  $query = "SELECT * FROM remember_tokens WHERE token = ? AND expiry > ?";
  $stmt = $this->conn->prepare($query);
  $current_time = time();
  $stmt->bind_param("si", $token, $current_time);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
   $remember_token = $result->fetch_assoc();
   $user_id = $remember_token['user_id'];
   $user_type = $remember_token['user_type'];

   // Retrieve user details based on user type
   if ($user_type === 'worker') {
    $user_query = "SELECT * FROM workers WHERE id_worker = ?";
   } else {
    $user_query = "SELECT * FROM admins WHERE id_admin = ?";
   }

   $user_stmt = $this->conn->prepare($user_query);
   $user_stmt->bind_param("i", $user_id);
   $user_stmt->execute();
   $user_result = $user_stmt->get_result();

   if ($user_result->num_rows > 0) {
    $user = $user_result->fetch_assoc();

    // Automatically log in the user
    if ($user_type === 'worker') {
     $this->setLoginSession(
      $user['id_worker'],
      $user['username'],
      $user['name_worker'],
      'worker',
      $user['id_position']
     );
    } else {
     $this->setLoginSession(
      $user['id_admin'],
      $user['username'],
      $user['name_admin'],
      'admin',
      $user['id_position']
     );
    }

    return true;
   }
  }

  // Invalid or expired token
  $this->clearRememberMeCookie();
  return false;
 }

 // Clear remember me cookie
 private function clearRememberMeCookie()
 {
  // Remove cookie from browser
  setcookie(
   $this->remember_cookie_name,
   '',
   time() - 3600,
   '/',
   '',
   true,
   true
  );

  // Remove token from database if exists
  if (isset($_COOKIE[$this->remember_cookie_name])) {
   $token = $_COOKIE[$this->remember_cookie_name];
   $query = "DELETE FROM remember_tokens WHERE token = ?";
   $stmt = $this->conn->prepare($query);
   $stmt->bind_param("s", $token);
   $stmt->execute();
  }
 }

 // Existing methods remain the same
 public function isLoggedIn()
 {
  return isset($_SESSION['user_id']) && isset($_SESSION['role']);
 }

 public function isAdmin()
 {
  return $this->isLoggedIn() && $_SESSION['role'] === 'admin';
 }

 public function isWorker()
 {
  return $this->isLoggedIn() && $_SESSION['role'] === 'worker';
 }

 public function requireRole($allowedRoles = ['worker', 'admin'])
 {
  // Check if user is logged in
  if (!$this->isLoggedIn()) {
   header("Location: login.php");
   exit();
  }

  // Check if current role is in allowed roles
  if (!in_array($_SESSION['role'], $allowedRoles)) {
   header("Location: unauthorized.php");
   exit();
  }

  return true;
 }

 public function getUserDetails()
 {
  if (!$this->isLoggedIn()) {
   return null;
  }

  return [
   'id' => $_SESSION['user_id'],
   'username' => $_SESSION['username'],
   'name' => $_SESSION['name'],
   'role' => $_SESSION['role'],
   'position_id' => $_SESSION['position_id']
  ];
 }

 public function logout()
 {
  // Clear remember me token from database
  if (isset($_COOKIE[$this->remember_cookie_name])) {
   $token = $_COOKIE[$this->remember_cookie_name];
   $query = "DELETE FROM remember_tokens WHERE token = ?";
   $stmt = $this->conn->prepare($query);
   $stmt->bind_param("s", $token);
   $stmt->execute();
  }

  // Unset all session variables
  $_SESSION = array();

  // Destroy the session
  session_destroy();

  // Delete the session cookie
  if (ini_get("session.use_cookies")) {
   $params = session_get_cookie_params();
   setcookie(
    session_name(),
    '',
    time() - 42000,
    $params["path"],
    $params["domain"],
    $params["secure"],
    $params["httponly"]
   );
  }

  // Clear remember me cookie
  $this->clearRememberMeCookie();

  // Redirect to login page
  header("Location: login.php");
  exit();
 }
}

// Initialize Authentication object
$auth = new Authentication($conn);

// Automatically check remember me on each page load
if (!$auth->isLoggedIn()) {
 $auth->checkRememberMeCookie();
}