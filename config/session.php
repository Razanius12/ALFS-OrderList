<?php
// Include database connection
require 'database.php';

/**
 * Authenticate user
 */

/**
 * Create remember me token
 */
function createRememberMeToken($user_id, $user_type)
{
 global $conn;

 // Generate a more secure token
 $token = bin2hex(random_bytes(32));

 // Set expiry (30 days from now)
 $expiry = time() + (30 * 24 * 60 * 60);

 // Delete any existing tokens for this user
 $delete_query = "DELETE FROM remember_tokens WHERE user_id = ? AND user_type = ?";
 $delete_stmt = $conn->prepare($delete_query);
 $delete_stmt->bind_param("is", $user_id, $user_type);
 $delete_stmt->execute();

 // Prepare and execute insert
 $insert_query = "INSERT INTO remember_tokens (user_id, user_type, token, expiry) VALUES (?, ?, ?, ?)";
 $stmt = $conn->prepare($insert_query);
 $stmt->bind_param("issi", $user_id, $user_type, $token, $expiry);

 // Set secure, HTTP-only cookie
 setcookie(
  'remember_me',
  $token,
  [
   'expires' => $expiry,
   'path' => '/',
   'domain' => '', // Current domain
   'secure' => true, // HTTPS only
   'httponly' => true, // Prevent JavaScript access
   'samesite' => 'Strict' // Prevent CSRF
  ]
 );

 return $stmt->execute();
}

/**
 * Check remember me token
 */
function checkRememberMeToken()
{
 global $conn;

 // Check if remember_me cookie exists
 if (!isset($_COOKIE['remember_me'])) {
  return false;
 }

 $token = $_COOKIE['remember_me'];

 // First, clean up expired tokens
 $cleanup_query = "DELETE FROM remember_tokens WHERE expiry < ?";
 $cleanup_stmt = $conn->prepare($cleanup_query);
 $current_time = time();
 $cleanup_stmt->bind_param("i", $current_time);
 $cleanup_stmt->execute();

 // Now check for valid token
 $query = "SELECT * FROM remember_tokens WHERE token = ? AND expiry > ?";
 $stmt = $conn->prepare($query);
 $stmt->bind_param("si", $token, $current_time);
 $stmt->execute();
 $result = $stmt->get_result();

 if ($result->num_rows > 0) {
  $token_data = $result->fetch_assoc();

  // Determine user type and fetch user details
  $user_query = $token_data['user_type'] == 'worker'
   ? "SELECT * FROM workers WHERE id_worker = ?"
   : "SELECT * FROM admins WHERE id_admin = ?";

  $user_stmt = $conn->prepare($user_query);
  $user_stmt->bind_param("i", $token_data['user_id']);
  $user_stmt->execute();
  $user_result = $user_stmt->get_result();

  if ($user_result->num_rows > 0) {
   $user = $user_result->fetch_assoc();

   // Start new session if needed
   if (session_status() == PHP_SESSION_NONE) {
    session_start();
   }

   // Regenerate session ID for security
   session_regenerate_id(true);

   // Set session variables
   $_SESSION['user_id'] = $token_data['user_id'];
   $_SESSION['username'] = $user['username'];
   $_SESSION['name'] = $token_data['user_type'] == 'worker'
    ? $user['name_worker']
    : $user['name_admin'];
   $_SESSION['role'] = $token_data['user_type'];
   $_SESSION['level'] = $token_data['user_type'];
   $_SESSION['position_id'] = $user['id_position'];

   // Refresh token
   refreshRememberMeToken($token_data['user_id'], $token_data['user_type']);

   return true;
  }
 }

 // Invalid or expired token
 clearRememberMeToken();
 return false;
}

// New function to refresh remember me token
function refreshRememberMeToken($user_id, $user_type)
{
 global $conn;

 // Generate a new token
 $new_token = bin2hex(random_bytes(32));
 $new_expiry = time() + (30 * 24 * 60 * 60);

 // Update token in database
 $update_query = "UPDATE remember_tokens SET token = ?, expiry = ? WHERE user_id = ? AND user_type = ?";
 $stmt = $conn->prepare($update_query);
 $stmt->bind_param("ssis", $new_token, $new_expiry, $user_id, $user_type);
 $stmt->execute();

 // Set new cookie
 setcookie(
  'remember_me',
  $new_token,
  [
   'expires' => $new_expiry,
   'path' => '/',
   'domain' => '', // Current domain
   'secure' => true, // HTTPS only
   'httponly' => true, // Prevent JavaScript access
   'samesite' => 'Strict' // Prevent CSRF
  ]
 );
}

// Function to clear remember me token
function clearRememberMeToken()
{
 global $conn;

 // Clear cookie
 setcookie('remember_me', '', time() - 3600, '/', '', true, true);

 // If token exists in database, remove it
 if (isset($_COOKIE['remember_me'])) {
  $token = $_COOKIE['remember_me'];
  $delete_query = "DELETE FROM remember_tokens WHERE token = ?";
  $stmt = $conn->prepare($delete_query);
  $stmt->bind_param("s", $token);
  $stmt->execute();
 }
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

 // Store user info before clearing session
 $user_id = $_SESSION['user_id'] ?? null;
 $user_type = $_SESSION['level'] ?? null;

 // First, clear the remember me token
 if ($user_id && $user_type) {
  // Delete token by user ID and type (more reliable than cookie-based deletion)
  $delete_query = "DELETE FROM remember_tokens WHERE user_id = ? AND user_type = ?";
  $stmt = $conn->prepare($delete_query);
  $stmt->bind_param("is", $user_id, $user_type);
  $stmt->execute();
 }

 // Also delete by token if cookie exists (as backup)
 if (isset($_COOKIE['remember_me'])) {
  $token = $_COOKIE['remember_me'];
  $delete_token_query = "DELETE FROM remember_tokens WHERE token = ?";
  $stmt = $conn->prepare($delete_token_query);
  $stmt->bind_param("s", $token);
  $stmt->execute();
 }

 // Clear remember me cookie
 if (isset($_COOKIE['remember_me'])) {
  // Clear for all possible paths and domains
  setcookie('remember_me', '', [
   'expires' => time() - 3600,
   'path' => '/',
   'domain' => '',
   'secure' => true,
   'httponly' => true,
   'samesite' => 'Strict'
  ]);
  unset($_COOKIE['remember_me']);
 }

 // Clear session
 $_SESSION = array();

 // Destroy session cookie
 if (ini_get("session.use_cookies")) {
  $params = session_get_cookie_params();
  setcookie(
   session_name(),
   '',
   [
    'expires' => time() - 3600,
    'path' => $params["path"],
    'domain' => $params["domain"],
    'secure' => $params["secure"],
    'httponly' => $params["httponly"],
    'samesite' => 'Strict'
   ]
  );
 }

 // Destroy session
 session_destroy();

 // Start new session to ensure clean state
 session_start();

 // Regenerate session ID
 session_regenerate_id(true);

 // Set cache headers
 header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
 header("Cache-Control: post-check=0, pre-check=0", false);
 header("Pragma: no-cache");
}

// Check remember me if not logged in
if (!isLoggedIn()) {
 checkRememberMeToken();
}

// Allowed Roles

// Shared Access Page Function
function sharedAccessPage($debugMode = true)
{
 // Ensure session is started
 if (session_status() == PHP_SESSION_NONE) {
  session_start();
 }

 return checkPageAccess(['admin', 'worker'], $debugMode);
}

// Admin Only Page Function
function adminOnlyPage($debugMode = true)
{
 // Ensure session is started
 if (session_status() == PHP_SESSION_NONE) {
  session_start();
 }

 return checkPageAccess(['admin'], $debugMode);
}

// Worker Only Page Function
function workerOnlyPage($debugMode = true)
{
 // Ensure session is started
 if (session_status() == PHP_SESSION_NONE) {
  session_start();
 }

 return checkPageAccess(['worker'], $debugMode);
}

// checkPageAccess function
function checkPageAccess($allowedRoles = [], $debugMode = true)
{
 $reason = ''; // Initialize reason for debugging

 // Enhanced session and authentication checks
 if (session_status() == PHP_SESSION_NONE) {
  $reason = "Session not started";
  redirectToUnauthorized($reason);
 }

 // More comprehensive login check
 if (
  empty($_SESSION['user_id']) ||
  empty($_SESSION['level']) ||
  empty($_SESSION['username'])
 ) {
  $reason = "Incomplete user authentication";
  redirectToUnauthorized($reason);
 }

 // Debugging current user details
 $currentUserDetails = [
  'user_id' => $_SESSION['user_id'] ?? 'N/A',
  'level' => $_SESSION['level'] ?? 'N/A',
  'username' => $_SESSION['username'] ?? 'N/A'
 ];

 // Role-based access check with more strict validation
 if (
  !empty($allowedRoles) &&
  (!isset($_SESSION['level']) || !in_array($_SESSION['level'], $allowedRoles))
 ) {
  $reason = sprintf(
   "Unauthorized role access. Current role: %s. Allowed roles: %s",
   $_SESSION['level'] ?? 'Unknown',
   implode(', ', $allowedRoles)
  );
  redirectToUnauthorized($reason);
 }

 // Optional debug mode to track access attempts
 if ($debugMode) {
  $_SESSION['last_access_attempt'] = [
   'timestamp' => date('Y-m-d H:i:s'),
   'user_details' => $currentUserDetails,
   'allowed_roles' => $allowedRoles,
   'remote_ip' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown'
  ];
 }

 return true;
}

// redirectToUnauthorized function
function redirectToUnauthorized($reason = '')
{
 // Prevent any output before redirect
 if (ob_get_level()) {
  ob_clean();
 }

 // Log unauthorized access attempt
 error_log("Unauthorized Access: " . $reason);

 // Prepare detailed reason
 $detailedReason = $reason ?: "Access Denied";

 // Clear sensitive session data
 $_SESSION['unauthorized_reason'] = $detailedReason;

 // Optional: destroy session for security
 session_destroy();

 // Regenerate session ID
 session_regenerate_id(true);

 // Redirect to unauthorized page
 header("HTTP/1.1 403 Forbidden");
 header("Location: main/common/unauthorized.php");
 exit();
}

function getCurrentUserDetails()
{
 // Ensure session is started
 if (session_status() == PHP_SESSION_NONE) {
  session_start();
 }

 // More robust user details retrieval
 if (!isset($_SESSION['user_id']) || !isset($_SESSION['level'])) {
  return [
   'id' => null,
   'username' => null,
   'name' => 'User',
   'role' => null
  ];
 }

 // Debug logging
 error_log("Current Session Details: " . print_r($_SESSION, true));

 // Prioritize the generic 'name' session variable
 $name = $_SESSION['name'] ?? null;

 // Fallback to role-specific names if needed
 if (!$name) {
  $name = ($_SESSION['level'] == 'admin')
   ? ($_SESSION['name_admin'] ?? null)
   : ($_SESSION['name_worker'] ?? null);
 }

 return [
  'id' => $_SESSION['user_id'],
  'username' => $_SESSION['username'] ?? null,
  'name' => $name ?? 'User',
  'role' => $_SESSION['level']
 ];
}

// Additional security check
function isUserAuthenticated()
{
 return isset($_SESSION['user_id']) &&
  isset($_SESSION['level']) &&
  isset($_SESSION['username']);
}