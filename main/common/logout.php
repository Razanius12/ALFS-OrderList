<?php
require '../../config/session.php';
session_start();

try {
 // More comprehensive logout
 if (isLoggedIn()) {
  // Perform logout with more aggressive cookie and session clearing
  logoutUser();
 } else {
  header('Content-Type: application/json');
  echo json_encode(['status' => 'error', 'message' => 'User  not logged in']);
  exit();

 }

 // Check if it's an AJAX request
 if (
  isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
 ) {
  header('Content-Type: application/json');
  echo json_encode(['status' => 'success', 'redirect' => 'main/common/login.php']);
  exit();
 } else {
  header("Location: main/common/login.php");
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
  header("Location: main/common/login.php");
  exit();
 }
}