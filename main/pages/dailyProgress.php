<?php
adminOnlyPage();
$currentUser = getCurrentUserDetails();
checkPageAccess();

// Database connection
require 'config/database.php';

// Get selected month and year, default to current month
$selectedMonth = isset($_GET['month']) ? $_GET['month'] : date('m');
$selectedYear = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Fetch daily income data for selected month
$query = "SELECT 
            DATE(start_date) AS order_date, 
            SUM(order_price) AS total_income 
          FROM orders 
          WHERE status = 'COMPLETED' 
            AND MONTH(start_date) = ? 
            AND YEAR(start_date) = ?
          GROUP BY DATE(start_date) 
          ORDER BY order_date";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $selectedMonth, $selectedYear);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Prepare data for Chart.js
$dates = [];
$incomes = [];

while ($row = mysqli_fetch_assoc($result)) {
 // Format the date to show only the day
 $dates[] = date('d', strtotime($row['order_date']));
 $incomes[] = $row['total_income'];
}

// Generate month and year options
$months = [];
for ($m = 1; $m <= 12; $m++) {
 $months[$m] = date('F', mktime(0, 0, 0, $m, 1));
}

$currentYear = date('Y');
$years = range($currentYear - 5, $currentYear + 5);
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
     <a href="./index.php?page=dailyProgress">Daily Progress</a>
    </li>
   </ul>
  </div>

  <div class="row">
   <div class="col-md-12 col-lg">
    <div class="card">
     <div class="card-header">
      <div class="d-flex align-items-center">
       <h4 class="card-title">
        Income Per Day -
        <?php echo $months[$selectedMonth] . ' ' . $selectedYear; ?>
       </h4>
       <button type="button" class="btn btn-primary btn-round ms-auto" data-toggle="modal" data-target="#filterModal">
        <i class="fas fa-filter"></i> Filter Progress
       </button>
      </div>
     </div>
     <div class="card-body">
      <div class="chart-container" style="position: relative; height: 400px; width: 100%;">
       <canvas id="dailyProgressBarChart"></canvas>
      </div>
     </div>
    </div>
   </div>
  </div>
 </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel"
 aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
  <div class="modal-content">
   <div class="modal-header">
    <h5 class="modal-title" id="filterModalLabel">Filter Daily Progress</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
     <span aria-hidden="true">&times;</span>
    </button>
   </div>
   <form method="GET" action="">
    <div class="modal-body">
     <input type="hidden" name="page" value="dailyProgress">

     <div class="form-group">
      <label>Select Month</label>
      <select name="month" class="form-control">
       <?php foreach ($months as $num => $name): ?>
        <option value="<?php echo $num; ?>" <?php echo ($num == $selectedMonth) ? 'selected' : ''; ?>>
         <?php echo $name; ?>
        </option>
       <?php endforeach; ?>
      </select>
     </div>

     <div class="form-group">
      <label>Select Year</label>
      <select name="year" class="form-control">
       <?php foreach ($years as $year): ?>
        <option value="<?php echo $year; ?>" <?php echo ($year == $selectedYear) ? 'selected' : ''; ?>>
         <?php echo $year; ?>
        </option>
       <?php endforeach; ?>
      </select>
     </div>
    </div>
    <div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
     <button type="submit" class="btn btn-primary">Apply Filter</button>
    </div>
   </form>
  </div>
 </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
 document.addEventListener('DOMContentLoaded', function () {
  $(document).ready(function () {

   // Initialize modal with explicit methods
   $('#filterModal').modal({
    show: false,
    backdrop: 'static',
    keyboard: true
   });

   // Add explicit trigger for modal
   $('[data-toggle="modal"]').on('click', function () {
    var target = $(this).data('target');
    $(target).modal('show');
   });

   // Ensure modal can be closed
   $(document).on('click', '[data-dismiss="modal"]', function () {
    $(this).closest('.modal').modal('hide');
   });

  });

  const ctx = document.getElementById('dailyProgressBarChart').getContext('2d');

  new Chart(ctx, {
   type: 'bar',
   data: {
    labels: <?php echo json_encode($dates); ?>,
    datasets: [{
     label: 'Daily Income',
     data: <?php echo json_encode($incomes); ?>,
     backgroundColor: 'rgba(54, 162, 235, 0.6)',
     borderColor: 'rgba(54, 162, 235, 1)',
     borderWidth: 1
    }]
   },
   options: {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
     y: {
      beginAtZero: true,
      title: {
       display: true,
       text: 'Income ($)'
      }
     },
     x: {
      title: {
       display: true,
       text: 'Date'
      }
     }
    }
   }
  });
 });
</script>


<style>
 @media (max-width: 767px) {
  .card-title {
   font-size: 16px !important;
  }

  .btn-round {
   font-size: 14px !important;
  }

  .chart-container {
   padding-left: 0 !important;
   padding-right: 0 !important;
  }
 }
</style>