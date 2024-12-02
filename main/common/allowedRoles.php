<?php
// Shared Access Page Function
if (!function_exists('sharedAccessPage')) {
 function sharedAccessPage($debugMode = true)
 {
  return checkPageAccess(['admin', 'worker'], $debugMode);
 }
}

// Admin Only Page Function
if (!function_exists('adminOnlyPage')) {
 function adminOnlyPage($debugMode = true)
 {
  return checkPageAccess(['admin'], $debugMode);
 }
}

// Worker Only Page Function
if (!function_exists('workerOnlyPage')) {
 function workerOnlyPage($debugMode = true)
 {
  return checkPageAccess(['worker'], $debugMode);
 }
}

// Existing checkPageAccess function (from previous response)
if (!function_exists('checkPageAccess')) {
 function checkPageAccess($allowedRoles = [], $debugMode = true)
 {
  $reason = ''; // Initialize reason for debugging

  // Check if session is initialized
  if (!isset($_SESSION)) {
   $reason = "Session not initialized";
   redirectToUnauthorized($reason);
  }

  // Check if user is logged in
  if (!isset($_SESSION['user_id']) || !isset($_SESSION['level'])) {
   $reason = "Missing user credentials";
   redirectToUnauthorized($reason);
  }

  // Debugging current user details
  $currentUserDetails = [
   'user_id' => $_SESSION['user_id'] ?? 'N/A',
   'level' => $_SESSION['level'] ?? 'N/A'
  ];

  // Role-based access check
  if (!empty($allowedRoles) && !in_array($_SESSION['level'], $allowedRoles)) {
   $reason = sprintf(
    "Unauthorized role access. Current role: %s. Allowed roles: %s",
    $_SESSION['level'],
    implode(', ', $allowedRoles)
   );
   redirectToUnauthorized($reason);
  }

  // Optional debug mode to track access attempts
  if ($debugMode) {
   $_SESSION['last_access_attempt'] = [
    'timestamp' => date('Y-m-d H:i:s'),
    'user_details' => $currentUserDetails,
    'allowed_roles' => $allowedRoles
   ];
  }

  return true;
 }
}

// Existing redirectToUnauthorized function
if (!function_exists('redirectToUnauthorized')) {
 function redirectToUnauthorized($reason = '')
 {
  // Prepare detailed reason
  $detailedReason = $reason ?: "Access Denied";

  // Clear any existing session data
  $_SESSION['unauthorized_reason'] = $detailedReason;

  // Debug information (optional)
  $debugInfo = [
   'timestamp' => date('Y-m-d H:i:s'),
   'reason' => $detailedReason,
   'user_id' => $_SESSION['user_id'] ?? 'N/A',
   'user_level' => $_SESSION['level'] ?? 'N/A'
  ];

  // Ensure no output before redirect
  ob_clean();
  header("Location: unauthorized.php");
  exit();
 }
}

// Existing getCurrentUserDetails function
if (!function_exists('getCurrentUserDetails')) {
 function getCurrentUserDetails()
 {
  if (!isset($_SESSION['user_id'])) {
   return null;
  }

  return [
   'id' => $_SESSION['user_id'] ?? null,
   'username' => $_SESSION['username'] ?? null,
   'name' => $_SESSION['level'] == 'admin'
    ? ($_SESSION['name_admin'] ?? null)
    : ($_SESSION['name_worker'] ?? null),
   'role' => $_SESSION['level'] ?? null
  ];
 }
}