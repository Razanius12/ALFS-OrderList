<!DOCTYPE html>
<html lang="en">

<?php

session_start();
require 'config/session.php';
$currentUser = getCurrentUserDetails();
checkPageAccess();
if (!isUserAuthenticated()) {
 redirectToUnauthorized("Authentication failed");
}

include_once 'head.php';
?>

<body>
 <div class="wrapper">
  <?php
  include_once 'main/bars/sidebar.php';
  ?>

  <div class="main-panel">
   <?php
   include_once 'main/bars/navbar.php';

   // check if "page" has parameter and prevent user from getting knowhere
   $page = $_GET['page'] ?? 'dashboard';

   // page redirects
   switch ($page) {
    case 'undefined':
     includePage('main/pages/dashboard.php');
     break;
    case 'admins':
     includePage('main/pages/admins.php');
     break;
    case 'dashboard':
     includePage('main/pages/dashboard.php');
     break;
    case 'task':
     includePage('main/pages/task.php');
     break;
    case 'orderData':
     includePage('main/pages/orderData.php');
     break;
    case 'workers':
     includePage('main/pages/workers.php');
     break;
    case 'addNewPosition':
     includePage('main/pages/addNewPosition.php');
     break;
    case 'alfOffices':
     includePage('main/pages/alfOffices.php');
     break;
    case 'dailyProgress':
     includePage('main/pages/dailyProgress.php');
     break;
     case 'profile':
      includePage('main/pages/profile.php');
      break;

    default:
     includePage('main/pages/dashboard.php');
     break;
   }

   // check if file exists
   function includePage($file)
   {
    if (file_exists($file)) {
     include $file;
    } else {
     // Redirect to dashboard if file doesn't exist
     header('Location: index.php?page=dashboard');
     exit();
    }
   }

   include_once 'footer.php';
   ?>
  </div>

 </div>

 <?php
 include_once 'script.php';
 ?>

</body>