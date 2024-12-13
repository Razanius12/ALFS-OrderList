<?php

sharedAccessPage();
$currentUser = getCurrentUserDetails();
checkPageAccess();

// Database connection
require 'config/database.php';

// Initialize counts
$adminCount = 0;
$workerCount = 0;
$orderCount = 0;
$officeCount = 0;

// Fetch counts
try {
 // Count admins
 $adminQuery = "SELECT COUNT(*) AS count FROM admins";
 $adminResult = mysqli_query($conn, $adminQuery);
 if ($adminResult) {
  $adminCount = mysqli_fetch_assoc($adminResult)['count'];
 }

 // Count workers
 $workerQuery = "SELECT COUNT(*) AS count FROM workers";
 $workerResult = mysqli_query($conn, $workerQuery);
 if ($workerResult) {
  $workerCount = mysqli_fetch_assoc($workerResult)['count'];
 }

 // Count orders
 $orderQuery = "SELECT COUNT(*) AS count FROM orders";
 $orderResult = mysqli_query($conn, $orderQuery);
 if ($orderResult) {
  $orderCount = mysqli_fetch_assoc($orderResult)['count'];
 }

 // Count offices
 $officeQuery = "SELECT COUNT(*) AS count FROM gmaps";
 $officeResult = mysqli_query($conn, $officeQuery);
 if ($officeResult) {
  $officeCount = mysqli_fetch_assoc($officeResult)['count'];
 }
} catch (Exception $e) {
 // Log the error
 error_log("Dashboard Count Error: " . $e->getMessage());
}
?>

<!-- Page content -->
<div class="container">
 <div class="page-inner">
  <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
   <div>
    <h3 class="fw-bold mb-3">Dashboard</h3>
    <h6 class="op-7 mb-2">Welcome to ALF Solution OrderList Web Application</h6>
   </div>
   <?php if ($currentUser['role'] === 'admin'): ?>
    <div class="ms-md-auto py-2 py-md-0">
     <a href="index.php?page=workers" class="btn btn-primary btn-round me-2">Manage Workers</a>
     <a href="index.php?page=orderData" class="btn btn-primary btn-round">Add Order</a>
    </div>
   <?php elseif ($currentUser['role'] === 'worker'): ?>
    <div class="ms-md-auto py-2 py-md-0">
     <a href="index.php?page=task" class="btn btn-primary btn-round">My Tasks</a>
    </div>
   <?php endif; ?>
  </div>
  <div class="row">
   <div class="col-sm-6 col-md-3">
    <div class="card card-stats card-round">
     <div class="card-body">
      <div class="row align-items-center">
       <div class="col-icon">
        <div class="icon-big text-center icon-primary bubble-shadow-small">
         <i class="fas fa-users"></i>
        </div>
       </div>
       <div class="col col-stats ms-3 ms-sm-0">
        <div class="numbers">
         <p class="card-category">Admins</p>
         <h4 class="card-title"><?php echo htmlspecialchars($adminCount); ?></h4>
        </div>
       </div>
      </div>
     </div>
    </div>
   </div>
   <div class="col-sm-6 col-md-3">
    <div class="card card-stats card-round">
     <div class="card-body">
      <div class="row align-items-center">
       <div class="col-icon">
        <div class="icon-big text-center icon-info bubble-shadow-small">
         <i class="fas fa-user-check"></i>
        </div>
       </div>
       <div class="col col-stats ms-3 ms-sm-0">
        <div class="numbers">
         <p class="card-category">Workers</p>
         <h4 class="card-title"><?php echo htmlspecialchars($workerCount); ?></h4>
        </div>
       </div>
      </div>
     </div>
    </div>
   </div>
   <div class="col-sm-6 col-md-3">
    <div class="card card-stats card-round">
     <div class="card-body">
      <div class="row align-items-center">
       <div class="col-icon">
        <div class="icon-big text-center icon-success bubble-shadow-small">
         <i class="fas fa-map-marker-alt"></i>
        </div>
       </div>
       <div class="col col-stats ms-3 ms-sm-0">
        <div class="numbers">
         <p class="card-category">Offices</p>
         <h4 class="card-title"><?php echo htmlspecialchars($officeCount); ?></h4>
        </div>
       </div>
      </div>
     </div>
    </div>
   </div>
   <div class="col-sm-6 col-md-3">
    <div class="card card-stats card-round">
     <div class="card-body">
      <div class="row align-items-center">
       <div class="col-icon">
        <div class="icon-big text-center icon-secondary bubble-shadow-small">
         <i class="far fa-check-circle"></i>
        </div>
       </div>
       <div class="col col-stats ms-3 ms-sm-0">
        <div class="numbers">
         <p class="card-category">Orders</p>
         <h4 class="card-title"><?php echo htmlspecialchars($orderCount); ?></h4>
        </div>
       </div>
      </div>
     </div>
    </div>
   </div>
  </div>
  <div class="row">
   <div class="page-category">
    We are currently working on the website <br>
    In the meantime, enjoy the current available feature
   </div>
  </div>
 </div>
</div>

<?php
// Close the database connection
mysqli_close($conn);
?>