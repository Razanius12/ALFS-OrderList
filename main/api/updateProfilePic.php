<?php
header('Content-Type: application/json');
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['level'])) {
 http_response_code(401);
 echo json_encode(['error' => 'Unauthorized']);
 exit();
}

$userId = $_SESSION['user_id'];
$userLevel = $_SESSION['level'];
$table = ($userLevel === 'admin') ? 'admins' : 'workers';
$idField = ($userLevel === 'admin') ? 'id_admin' : 'id_worker';

// Function to delete the old profile picture
function deleteOldProfilePic($filePath)
{
 if ($filePath && file_exists(__DIR__ . '/../../' . $filePath)) {
  unlink(__DIR__ . '/../../' . $filePath);
 }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 if (isset($_POST['action'])) {
  switch ($_POST['action']) {
   case 'upload':
    if (!isset($_FILES['profile_pic'])) {
     echo json_encode(['error' => 'No file uploaded']);
     exit();
    }

    $file = $_FILES['profile_pic'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

    if (!in_array($file['type'], $allowedTypes)) {
     echo json_encode(['error' => 'Invalid file type. Please upload JPEG, PNG, or WebP images only.']);
     exit();
    }

    $maxSize = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $maxSize) {
     echo json_encode(['error' => 'File size too large. Maximum size is 5MB.']);
     exit();
    }

    $uploadDir = __DIR__ . '/../imgdata/profile/';
    if (!file_exists($uploadDir)) {
     mkdir($uploadDir, 0777, true);
    }

    // Get current profile pic to delete
    $stmt = $conn->prepare("SELECT profile_pic FROM $table WHERE $idField = ?");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();

    $fileName = uniqid() . '_' . basename($file['name']);
    $uploadPath = $uploadDir . $fileName;
    $dbPath = 'main/imgdata/profile/' . $fileName;

    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
     deleteOldProfilePic($userData['profile_pic']);

     $updateQuery = "UPDATE $table SET profile_pic = ? WHERE $idField = ?";
     $updateStmt = $conn->prepare($updateQuery);
     $updateStmt->bind_param('si', $dbPath, $userId);

     if ($updateStmt->execute()) {
      $_SESSION['profile_pic'] = $dbPath;
      echo json_encode([
       'success' => true,
       'message' => 'Profile picture updated successfully',
       'profile_pic' => $dbPath
      ]);
     } else {
      echo json_encode(['error' => 'Database update failed']);
     }
    } else {
     echo json_encode(['error' => 'Failed to upload file']);
    }
    break;

   case 'delete':
    $stmt = $conn->prepare("SELECT profile_pic FROM $table WHERE $idField = ?");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();

    deleteOldProfilePic($userData['profile_pic']);

    $updateQuery = "UPDATE $table SET profile_pic = NULL WHERE $idField = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param('i', $userId);

    if ($updateStmt->execute()) {
     $_SESSION['profile_pic'] = null;
     echo json_encode([
      'success' => true,
      'message' => 'Profile picture deleted successfully'
     ]);
    } else {
     echo json_encode(['error' => 'Failed to delete profile picture']);
    }
    break;

   default:
    echo json_encode(['error' => 'Invalid action']);
    break;
  }
 } else {
  echo json_encode(['error' => 'No action specified']);
 }
} else {
 http_response_code(405);
 echo json_encode(['error' => 'Method not allowed']);
}