<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/session.php';

header('Content-Type: application/json');

try {
 $username = $_POST['username'] ?? '';
 $password = $_POST['password'] ?? '';
 $remember_me = isset($_POST['remember_me']);

 // Validate input
 if (empty($username) || empty($password)) {
  http_response_code(400);
  echo json_encode(['message' => 'Username and password are required']);
  exit();
 }

 // Attempt login with remember me option
 $loginResult = $auth->login($username, $password, $remember_me);

 if ($loginResult) {
  // Use a clean, relative path
  $redirect = '../../index.php';

  echo json_encode([
   'status' => 'success',
   'redirect' => $redirect
  ]);
 } else {
  // Failed login
  http_response_code(401);
  echo json_encode(['message' => 'Invalid username or password']);
 }
} catch (Exception $e) {
 // Handle any unexpected errors
 http_response_code(500);
 echo json_encode(['message' => 'An unexpected error occurred']);

 // Log the actual error
 error_log("Login Error: " . $e->getMessage());
}
?>