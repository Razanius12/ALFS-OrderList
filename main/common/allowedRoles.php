<?php
// allowedRoles.php - Comprehensive Debug Version

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure session is started
if (session_status() == PHP_SESSION_NONE) {
 session_start();
}

// Debugging function to log access attempts
function logAccessAttempt($message)
{
 $logFile = '../logs/access_log.txt';
 $timestamp = date('Y-m-d H:i:s');
 $logMessage = "[{$timestamp}] {$message}\n";
 file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Comprehensive page access check function
function checkPageAccess($allowedRoles = [])
{
 // Log the access attempt
 logAccessAttempt("Access attempt - Roles: " . implode(', ', $allowedRoles));

 // Detailed session debugging
 logAccessAttempt("Session Status: " . session_status());
 logAccessAttempt("Session ID: " . session_id());

 // Check session variables
 if (!isset($_SESSION)) {
  logAccessAttempt("Session not initialized");
  redirectToLogin("Session not initialized");
 }

 // Detailed login check
 if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
  logAccessAttempt("User not logged in - Missing user_id or role");
  redirectToLogin("Missing user credentials");
 }

 // Log current user details
 logAccessAttempt("Current User - ID: {$_SESSION['user_id']}, Role: {$_SESSION['role']}");

 // Role-based access check
 if (!empty($allowedRoles) && !in_array($_SESSION['role'], $allowedRoles)) {
  logAccessAttempt("Unauthorized access attempt - Current Role: {$_SESSION['role']}");
  redirectToUnauthorized("Role not authorized");
 }

 return true;
}

// Centralized redirection functions
function redirectToLogin($reason = '')
{
 logAccessAttempt("Redirecting to login - Reason: {$reason}");

 // Clear any existing session data
 $_SESSION = [];
 session_destroy();

 // Construct full login URL with error parameter
 $loginUrl = '../login.php?error=' . urlencode($reason);

 // Ensure no output before redirect
 ob_clean();
 header("Location: {$loginUrl}");
 exit();
}

function redirectToUnauthorized($reason = '')
{
 logAccessAttempt("Redirecting to unauthorized - Reason: {$reason}");

 // Construct full unauthorized URL with error parameter
 $unauthorizedUrl = '../unauthorized.php?error=' . urlencode($reason);

 // Ensure no output before redirect
 ob_clean();
 header("Location: {$unauthorizedUrl}");
 exit();
}

// Specific role-based access functions
function adminOnlyPage()
{
 checkPageAccess(['admin']);
}

function workerOnlyPage()
{
 checkPageAccess(['worker']);
}

function sharedAccessPage()
{
 checkPageAccess(['admin', 'worker']);
}

// User details retrieval
function getCurrentUserDetails()
{
 if (!isset($_SESSION['user_id'])) {
  return null;
 }

 return [
  'id' => $_SESSION['user_id'] ?? null,
  'username' => $_SESSION['username'] ?? null,
  'name' => $_SESSION['name'] ?? null,
  'role' => $_SESSION['role'] ?? null
 ];
}