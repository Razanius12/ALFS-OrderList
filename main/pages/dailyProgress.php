<?php
require 'main/common/allowedRoles.php';
try {
 sharedAccessPage();
 
 // Get current user details
 $currentUser = getCurrentUserDetails();
 ?>

 <div class="container">
  <div class="page-inner">
   <div class="page-header mb-0">
    <h3 class="fw-bold mb-3">Daily Progress</h3>
    <ul class="breadcrumbs mb-3">
     <li class="nav-home">
      <a href="./index.php">
       <i class="icon-home"></i>
      </a>
     </li>
     <li class="separator">
      <i class="icon-arrow-right"></i>
     </li>
     <li class="nav-item">
      <a href="./index.php?page=alfOffices">Daily Progress</a>
     </li>
    </ul>
   </div>
   <div class="page-category">
    Simple yet flexible JavaScript charting for designers &
    developers. Please checkout their
    <a href="http://www.chartjs.org/" target="_blank">full documentation</a>.
    <br>
    <br>
    wip, all placeholder
   </div>
   <div class="row">

    <div class="col-md-12">
     <div class="card">
      <div class="card-header">
       <div class="card-title">Daily Client Requests</div>
      </div>
      <div class="card-body">
       <div class="chart-container">
        <canvas id="dailyProgressBarChart"></canvas>
       </div>
      </div>
     </div>
    </div>

    <div class="col-md-12">
     <div class="card">
      <div class="card-header">
       <div class="card-title">Monthly Sales</div>
      </div>
      <div class="card-body">
       <div class="chart-container">
        <canvas id="dailyProgressBarChart"></canvas>
       </div>
      </div>
     </div>
    </div>

   </div>
  </div>
 </div>

 <script>
  <?php
  include 'main/js/dailyProgress.js';
  ?>
 </script>
 <?php
} catch (Exception $e) {
 // Handle any unexpected errors
}
?>