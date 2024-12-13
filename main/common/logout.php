<?php
require '../../config/session.php';
session_start();

try {
 // More comprehensive logout
 if (isLoggedIn()) {
  // Perform logout with more aggressive cookie and session clearing
  logoutUser();

  // Additional cookie clearing
  $cookies = $_COOKIE;
  foreach ($cookies as $name => $value) {
   setcookie($name, '', time() - 3600, '/');
   setcookie($name, '', time() - 3600, '/', $_SERVER['HTTP_HOST']);
   unset($_COOKIE[$name]);
  }

  // Regenerate session ID
  session_regenerate_id(true);
 }

 // Check if it's an AJAX request
 if (
  isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
 ) {
  header('Content-Type: application/json');
  echo json_encode(['status' => 'success', 'redirect' => 'login.php']);
  exit();
 } else {
  header("Location: login.php");
  exit();
 }
} catch (Exception $e) {
 error_log("Logout Error: " . $e->getMessage());

 if (
  isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
 ) {
  header('Content-Type: application/json');
  http_response_code(500);
  echo json_encode([
   'status' => 'error',
   'message' => 'Logout failed'
  ]);
  exit();
 } else {
  $_SESSION['logout_error'] = "An error occurred during logout. Please try again.";
  header("Location: login.php");
  exit();
 }
}