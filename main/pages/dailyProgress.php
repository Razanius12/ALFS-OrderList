<?php

adminOnlyPage();
$currentUser = getCurrentUserDetails();
checkPageAccess();

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
   Simple yet flexible JavaScript charting for designers & developers. Please checkout their
   <a href="http://www.chartjs.org/" target="_blank">full documentation</a>.
  </div>

  <!-- Button to trigger modal -->
  <button class="btn btn-primary" id="openModal"> Penghasilan Per Bulan</button>

  <!-- Modal -->
  <div class="modal" id="incomeModal" style="display: none;">
   <div class="modal-dialog" id="modalDialog">
    <div class="modal-content">
     <div class="modal-header">
      <h5 class="modal-title">Penghasilan Per Bulan</h5>
      <button type="button" class="btn-close" id="closeModal"></button>
     </div>
     <div class="modal-body">
      <div class="d-flex flex-wrap mb-3">
       <?php
       $months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
       foreach ($months as $month): ?>
        <div class="me-3 mb-2">
         <label for="income<?= $month ?>" class="form-label"><?= $month ?>:</label>
         <input type="text" id="income<?= $month ?>" class="form-control" style="width: 150px;" placeholder="Penghasilan"
          oninput="formatInput(this)">
        </div>
       <?php endforeach; ?>
      </div>
     </div>
     <div class="modal-footer">
      <button type="button" class="btn btn-success" id="saveIncome">Save</button>
      <button type="button" class="btn btn-secondary" id="closeModalFooter">Close</button>
     </div>
    </div>
   </div>
  </div>

  <!-- Chart Section -->
  <div class="row">
   <div class="col-md-12">
    <div class="card">
     <div class="card-header">
      <div class="card-title">Penghasilan Per Bulan</div>
     </div>
     <div class="card-body">
      <div class="chart-container">
       <canvas id="monthlyProgressBarChart"></canvas>
      </div>
     </div>
    </div>
   </div>
  </div>

 </div>
</div>

<script src="main/js/dailyProgress.js"></script>