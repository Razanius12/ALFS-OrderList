<?php
// Include session management
require_once '../../config/session.php';

try {
 // Check if user is logged in
 if (isLoggedIn()) {
  // Perform logout
  logoutUser();

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
 }

 // Check if it's an AJAX request
 if (
  isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
 ) {
  // AJAX request
  header('Content-Type: application/json');
  echo json_encode(['status' => 'success', 'redirect' => 'login.php']);
  exit();
 } else {
  // Regular request
  header("Location: login.php");
  exit();
 }
} catch (Exception $e) {
 // Log any errors
 error_log("Logout Error: " . $e->getMessage());

 // Check if it's an AJAX request
 if (
  isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
 ) {
  // AJAX request
  header('Content-Type: application/json');
  http_response_code(500);
  echo json_encode([
   'status' => 'error',
   'message' => 'Logout failed'
  ]);
  exit();
 } else {
  // Set error message in session for regular request
  $_SESSION['logout_error'] = "An error occurred during logout. Please try again.";
  header("Location: login.php");
  exit();
 }
}