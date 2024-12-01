<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start output buffering
ob_start();

// Include necessary configurations
require_once '../../config/database.php';
require_once '../../config/session.php';

// Logging function for debugging
function logLogoutAttempt($message)
{
 error_log(
  "[LOGOUT] " . $message . " | User: " .
  ($_SESSION['username'] ?? 'Unknown') .
  " | Time: " . date('Y-m-d H:i:s')
 );
}

try {
 // Check if user is logged in before attempting logout
 if ($auth->isLoggedIn()) {
  // Log the logout attempt
  logLogoutAttempt("Logout initiated");

  // Perform logout
  $auth->logout();

  // Destroy all session data
  session_unset();
  session_destroy();

  // Clear session cookie
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
 } else {
  // Log if logout attempted without active session
  logLogoutAttempt("Logout attempted without active session");
 }

 // Clear output buffer
 while (ob_get_level()) {
  ob_end_clean();
 }

 // Respond based on request type
 if (
  isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
 ) {
  // AJAX request
  header('Content-Type: application/json');
  echo json_encode(['status' => 'success']);
  exit();
 } else {
  // Regular request
  header("Location: login.php");
  exit();
 }
} catch (Exception $e) {
 // Clear output buffer
 while (ob_get_level()) {
  ob_end_clean();
 }

 // Log any unexpected errors
 error_log("[LOGOUT ERROR] " . $e->getMessage());

 // Respond with error
 if (
  isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
 ) {
  header('Content-Type: application/json');
  http_response_code(500);
  echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
  exit();
 } else {
  // Set error message in session
  $_SESSION['logout_error'] = "An error occurred during logout. Please try again.";
  header("Location: login.php");
  exit();
 }
}