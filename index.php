<!DOCTYPE html>
<html lang="en">

<?php
include_once 'head.php';
?>

<body>
 <div class="wrapper">
  <?php

  // check if "sidebar" has parameter and prevent user from getting knowhere
  $sidebar = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

  // sidebar for each pages
  switch ($sidebar) {
   case 'undefined':
    includePage('main/sidebars/sidebarDashboard.php');
    break;
    case 'admins':
     includePage('main/sidebars/sidebarDefault.php');
     break;
   case 'dashboard':
    includePage('main/sidebars/sidebarDashboard.php');
    break;
   case 'orderData':
    includePage('main/sidebars/sidebarOrderData.php');
    break;
   case 'workers':
    includePage('main/sidebars/sidebarWorkers.php');
    break;
   case 'addNewPosition':
    includePage('main/sidebars/sidebarWorkers.php');
    break;
   case 'alfOffices':
    includePage('main/sidebars/sidebarAlfOffices.php');
    break;
   case 'dailyProgress':
    includePage('main/sidebars/sidebarDailyProgress.php');
    break;

   default:
    includePage('main/sidebars/sidebarDashboard.php');
    break;
  }

  ?>

  <div class="main-panel">
   <?php
   include_once 'navbar.php';

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