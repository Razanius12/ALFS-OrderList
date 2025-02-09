<?php
adminOnlyPage();
$currentUser = getCurrentUserDetails();
checkPageAccess();

require 'config/database.php';

$selectedMonth = isset($_GET['month']) && !empty($_GET['month']) ? intval($_GET['month']) : date('m');
$selectedYear = isset($_GET['year']) && !empty($_GET['year']) ? intval($_GET['year']) : date('Y');

// Query for worker statistics
$query = "SELECT 
    w.id_worker,
    w.name_worker,
    COUNT(CASE WHEN o.status = 'COMPLETED' 
               AND MONTH(o.finished_at) = ? 
               AND YEAR(o.finished_at) = ? THEN 1 END) as completed_orders,
    COUNT(CASE WHEN o.status = 'PENDING'
               AND MONTH(o.start_date) = ? 
               AND YEAR(o.start_date) = ? THEN 1 END) as pending_orders,
    COUNT(CASE WHEN o.status = 'IN_PROGRESS'
               AND MONTH(o.start_date) = ? 
               AND YEAR(o.start_date) = ? THEN 1 END) as in_progress_orders,
    COUNT(CASE WHEN o.status = 'CANCELLED'
               AND MONTH(o.start_date) = ? 
               AND YEAR(o.start_date) = ? THEN 1 END) as cancelled_orders,
    COALESCE(SUM(CASE WHEN o.status = 'COMPLETED' 
                      AND MONTH(o.finished_at) = ? 
                      AND YEAR(o.finished_at) = ? 
                 THEN o.order_price ELSE 0 END), 0) as total_earnings
FROM 
    workers w
LEFT JOIN 
    orders o ON w.id_worker = o.worker_id
GROUP BY 
    w.id_worker, w.name_worker
ORDER BY 
    total_earnings DESC";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param(
 $stmt,
 "iiiiiiiiii", // 10 placeholders
 $selectedMonth,
 $selectedYear, // For completed orders
 $selectedMonth,
 $selectedYear, // For pending orders
 $selectedMonth,
 $selectedYear, // For in-progress orders
 $selectedMonth,
 $selectedYear, // For cancelled orders
 $selectedMonth,
 $selectedYear  // For total earnings
);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$monthlyTotalsQuery = "SELECT 
    SUM(order_price) AS total_monthly_income,
    COUNT(*) AS total_monthly_orders,
    ROUND(AVG(order_price), 2) AS monthly_average_order
FROM orders 
WHERE status = 'COMPLETED' 
    AND MONTH(finished_at) = ? 
    AND YEAR(finished_at) = ?";

$monthlyStmt = mysqli_prepare($conn, $monthlyTotalsQuery);
mysqli_stmt_bind_param($monthlyStmt, "ii", $selectedMonth, $selectedYear);
mysqli_stmt_execute($monthlyStmt);
$monthlyTotals = mysqli_fetch_assoc(mysqli_stmt_get_result($monthlyStmt));

// Then get the daily breakdown
$dailyQuery = "SELECT 
    DATE(finished_at) AS order_date, 
    SUM(order_price) AS total_income,
    COUNT(*) AS order_count,
    ROUND(AVG(order_price), 2) AS average_order_value,
    MAX(order_price) AS highest_order,
    MIN(order_price) AS lowest_order
FROM orders 
WHERE status = 'COMPLETED' 
    AND MONTH(finished_at) = ? 
    AND YEAR(finished_at) = ?
GROUP BY DATE(finished_at) 
ORDER BY order_date";

$dailyStmt = mysqli_prepare($conn, $dailyQuery);
mysqli_stmt_bind_param($dailyStmt, "ii", $selectedMonth, $selectedYear);
mysqli_stmt_execute($dailyStmt);
$dailyResult = mysqli_stmt_get_result($dailyStmt);

// Initialize arrays for the chart data
$dates = [];
$incomes = [];
$orderCounts = [];
$avgOrderValues = [];
$highestOrders = [];
$lowestOrders = [];

// Process daily data
while ($row = mysqli_fetch_assoc($dailyResult)) {
 $dates[] = date('d', strtotime($row['order_date']));
 $incomes[] = floatval($row['total_income']);
 $orderCounts[] = intval($row['order_count']);

 if ($row['order_count'] > 0) {
  $avgOrderValues[] = round(floatval($row['average_order_value']), 2);
 } else {
  $avgOrderValues[] = null;
 }

 $highestOrders[] = floatval($row['highest_order']);
 $lowestOrders[] = floatval($row['lowest_order']);
}

// Use the monthly totals from our first query
$totalIncome = floatval($monthlyTotals['total_monthly_income']);
$totalWorkerOrdersChart = intval($monthlyTotals['total_monthly_orders']);
$overallAvgOrderValue = floatval($monthlyTotals['monthly_average_order']);

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
   <h3 class="fw-bold mb-3">Monthly Recap</h3>
   <ul class="breadcrumbs mb-3">
    <li class="nav-home">
     <a href="./index.php"><i class="icon-home"></i></a>
    </li>
    <li class="separator"><i class="icon-arrow-right"></i></li>
    <li class="nav-item">
     <a href="./index.php?page=monthlyRecap">Monthly Recap</a>
    </li>
   </ul>
   <div class="ms-md-auto py-2 py-md-0 mb-2">
    <button class="btn btn-primary btn-round" data-toggle="modal" data-target="#exportModal">
     <i class="fas fa-file-pdf"></i> Export to PDF
    </button>
    </a>
    <button class="btn btn-primary btn-round" data-toggle="modal" data-target="#filterModal">
     <i class="fas fa-filter"></i> Filter
    </button>
   </div>
  </div>
  <div class="page-category">Shows worker perfomance report and monthly earnings chart</div>

  <!-- Statistics Summary -->
  <div class="row mb-4">
   <div class="col-md-3">
    <div class="card card-stats">
     <div class="card-body">
      <h6 class="card-title">Total Income</h6>
      <h4 class="card-value">$<?= number_format($totalIncome, 2) ?></h4>
     </div>
    </div>
   </div>
   <div class="col-md-3">
    <div class="card card-stats">
     <div class="card-body">
      <h6 class="card-title">Total Orders</h6>
      <h4 class="card-value"><?= $totalWorkerOrdersChart ?></h4>
     </div>
    </div>
   </div>
   <div class="col-md-3">
    <div class="card card-stats">
     <div class="card-body">
      <h6 class="card-title">Avg Order Value</h6>
      <h4 class="card-value">$<?= number_format($overallAvgOrderValue, 2) ?></h4>
     </div>
    </div>
   </div>
   <div class="col-md-3">
    <div class="card card-stats">
     <div class="card-body">
      <h6 class="card-title">Daily Average</h6>
      <h4 class="card-value">
       <?= count($dates) > 0 ? round($totalWorkerOrdersChart / count($dates), 1) : 0 ?> orders
      </h4>
     </div>
    </div>
   </div>
  </div>

  <!-- Daily Progress Chart Card -->
  <div class="row mb-4">
   <div class="col-md-12">
    <div class="card">
     <div class="card-header">
      <div class="d-flex align-items-center">
       <h4 class="card-title">Daily Income Progress - <?= $months[intval($selectedMonth)] . ' ' . $selectedYear; ?></h4>
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

  <!-- Worker Performance Card -->
  <div class="row">
   <div class="col-md-12">
    <div class="card">
     <div class="card-header">
      <div class="d-flex align-items-center">
       <h4 class="card-title">Worker Performance -
        <?= $months[intval($selectedMonth)] . ' ' . $selectedYear; ?>
       </h4>
      </div>
     </div>
     <div class="card-body">
      <div class="table-responsive">
       <table id="recap-table" class="display table table-striped table-hover">
        <thead>
         <tr>
          <th>Worker Name</th>
          <th style="width: 10%">Completed Orders</th>
          <th>Orders Status Ratio</th>
          <th>Total Earnings</th>
         </tr>
        </thead>
        <tbody>
         <?php while ($row = mysqli_fetch_assoc($result)):
          $totalWorkerOrders = $row['completed_orders'] + $row['pending_orders'] +
           $row['in_progress_orders'] + $row['cancelled_orders'];

          if ($totalWorkerOrders > 0) {
           $ratio = [
            'completed' => round(($row['completed_orders'] / $totalWorkerOrders) * 100),
            'pending' => round(($row['pending_orders'] / $totalWorkerOrders) * 100),
            'in_progress' => round(($row['in_progress_orders'] / $totalWorkerOrders) * 100),
            'cancelled' => round(($row['cancelled_orders'] / $totalWorkerOrders) * 100)
           ];
          } else {
           $ratio = [
            'completed' => 0,
            'pending' => 0,
            'in_progress' => 0,
            'cancelled' => 0
           ];
          }
          ?>
          <tr>
           <td><?= htmlspecialchars($row['name_worker']) ?></td>
           <td><?= $row['completed_orders'] ?></td>
           <td>
            <?php if ($totalWorkerOrders == 0): ?>
             <div class="text-muted">No orders yet</div>
            <?php else: ?>
             <div class="progress" style="height: 20px;">
              <?php if ($ratio['completed'] > 0): ?>
               <div class="progress-bar bg-success" style="width: <?= $ratio['completed'] ?>%"
                title="Completed: <?= $row['completed_orders'] ?>">
                <?= $ratio['completed'] ?>%
               </div>
              <?php endif; ?>
              <?php if ($ratio['pending'] > 0): ?>
               <div class="progress-bar bg-warning" style="width: <?= $ratio['pending'] ?>%"
                title="Pending: <?= $row['pending_orders'] ?>">
                <?= $ratio['pending'] ?>%
               </div>
              <?php endif; ?>
              <?php if ($ratio['in_progress'] > 0): ?>
               <div class="progress-bar bg-info" style="width: <?= $ratio['in_progress'] ?>%"
                title="In Progress: <?= $row['in_progress_orders'] ?>">
                <?= $ratio['in_progress'] ?>%
               </div>
              <?php endif; ?>
              <?php if ($ratio['cancelled'] > 0): ?>
               <div class="progress-bar bg-danger" style="width: <?= $ratio['cancelled'] ?>%"
                title="Cancelled: <?= $row['cancelled_orders'] ?>">
                <?= $ratio['cancelled'] ?>%
               </div>
              <?php endif; ?>
             </div>
            <?php endif; ?>
           </td>
           <td>$<?= number_format($row['total_earnings']) ?></td>
          </tr>
         <?php endwhile; ?>
        </tbody>
       </table>
       <div class="mt-3">
        <div class="d-flex align-items-center gap-3">
         <span class="fw-bold">Status Colors:</span>
         <div class="d-flex align-items-center gap-2">
          <div class="d-flex align-items-center">
           <div class="badge bg-success">&nbsp;</div>
           <span class="ms-1">Completed</span>
          </div>
          <div class="d-flex align-items-center">
           <div class="badge bg-warning">&nbsp;</div>
           <span class="ms-1">Pending</span>
          </div>
          <div class="d-flex align-items-center">
           <div class="badge bg-info">&nbsp;</div>
           <span class="ms-1">In Progress</span>
          </div>
          <div class="d-flex align-items-center">
           <div class="badge bg-danger">&nbsp;</div>
           <span class="ms-1">Cancelled</span>
          </div>
         </div>
        </div>
       </div>
      </div>
     </div>
    </div>
   </div>
  </div>

 </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered" role="document">
  <div class="modal-content">
   <div class="modal-header">
    <h5 class="modal-title">Filter Monthly Recap</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
     <span aria-hidden="true">&times;</span>
    </button>
   </div>
   <form method="GET" action="" id="filterForm">
    <div class="modal-body">
     <input type="hidden" name="page" value="monthlyRecap">
     <div class="form-group">
      <label>Select Month and Year</label>
      <input type="month" class="form-control" id="monthYearPicker" required
       value="<?php echo $selectedYear . '-' . str_pad($selectedMonth, 2, '0', STR_PAD_LEFT); ?>">
      <input type="hidden" name="month" id="monthInput">
      <input type="hidden" name="year" id="yearInput">
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

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered" role="document">
  <div class="modal-content">
   <div class="modal-header">
    <h5 class="modal-title">Export to PDF</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
     <span aria-hidden="true">&times;</span>
    </button>
   </div>
   <form method="GET" action="main/api/exportToPdf.php" id="exportForm">
    <div class="modal-body">
     <div class="form-group">
      <label>Select Month and Year</label>
      <div class="row">
       <div class="col">
        <select class="form-control" id="exportMonthDropdown" name="month">
         <option value="">Select Month</option>
         <?php foreach ($months as $num => $name): ?>
          <option value="<?= $num ?>"><?= $name ?></option>
         <?php endforeach; ?>
        </select>
       </div>
       <div class="col">
        <select class="form-control" id="exportYearDropdown" name="year">
         <option value="">Select Year</option>
         <?php foreach ($years as $year): ?>
          <option value="<?= $year ?>"><?= $year ?></option>
         <?php endforeach; ?>
        </select>
       </div>
      </div>
     </div>
     <div class="form-group">
      <label>Export Options</label>
      <div class="form-check">
       <input class="form-check-input" type="radio" name="exportOption" id="exportDailyStatistics"
        value="dailyStatistics" checked>
       <label class="form-check-label" for="exportDailyStatistics">
        Daily Statistics
       </label>
      </div>
      <div class="form-check">
       <input class="form-check-input" type="radio" name="exportOption" id="exportWorkerPerformance"
        value="workerPerformance">
       <label class="form-check-label" for="exportWorkerPerformance">
        Worker Performance
       </label>
      </div>
      <div class="form-check">
       <input class="form-check-input" type="radio" name="exportOption" id="exportAll" value="allData">
       <label class="form-check-label" for="exportAll">
        Export All
       </label>
      </div>
     </div>
    </div>
    <div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
     <button type="submit" class="btn btn-primary">Export</button>
    </div>
   </form>
  </div>
 </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
 document.addEventListener('DOMContentLoaded', function () {
  $(document).ready(function () {
   // Initialize DataTable
   $('#recap-table').DataTable({
    pageLength: 25,
    order: [[3, 'desc']],
    responsive: true,
    language: {
     search: "_INPUT_",
     searchPlaceholder: "Search records..."
    }
   });

   const exportMonthDropdown = document.getElementById('exportMonthDropdown');
   const exportYearDropdown = document.getElementById('exportYearDropdown');
   const exportAllRadio = document.getElementById('exportAll');
   const exportDailyStatisticsRadio = document.getElementById('exportDailyStatistics');
   const exportWorkerPerformanceRadio = document.getElementById('exportWorkerPerformance');
   const exportForm = document.getElementById('exportForm');

   // Disable month and year dropdowns if export all data is selected by default
   if (exportAllRadio.checked) {
    exportMonthDropdown.disabled = true;
    exportYearDropdown.disabled = true;
   }

   // Disable month and year dropdowns if export all data is selected
   exportAllRadio.addEventListener('change', function () {
    exportMonthDropdown.disabled = true;
    exportYearDropdown.disabled = true;
    exportMonthDropdown.value = '';
    exportYearDropdown.value = '';
   });

   // Enable month and year dropdowns if daily statistics or worker performance is selected
   exportDailyStatisticsRadio.addEventListener('change', function () {
    exportMonthDropdown.disabled = false;
    exportYearDropdown.disabled = false;
   });

   exportWorkerPerformanceRadio.addEventListener('change', function () {
    exportMonthDropdown.disabled = false;
    exportYearDropdown.disabled = false;
   });

   // Prevent form submission if no date is selected and export all data is not selected
   exportForm.addEventListener('submit', function (e) {
    if (!exportAllRadio.checked && (!exportMonthDropdown.value || !exportYearDropdown.value)) {
     e.preventDefault();
     Swal.fire({
      icon: 'warning',
      title: 'Incomplete Selection',
      text: 'Please select a month and year or select "Export All"',
     });
    }
   });

   // Initialize modal handlers
   $('#filterModal').modal({
    show: false,
    backdrop: 'static',
    keyboard: true
   });

   $('[data-toggle="modal"]').on('click', function () {
    var target = $(this).data('target');
    $(target).modal('show');
   });

   $(document).on('click', '[data-dismiss="modal"]', function () {
    $(this).closest('.modal').modal('hide');
   });

   // Initialize Daily Progress Chart
   const ctx = document.getElementById('dailyProgressBarChart').getContext('2d');

   // Custom gradient for income bars
   const incomeGradient = ctx.createLinearGradient(0, 0, 0, 400);
   incomeGradient.addColorStop(0, 'rgba(116, 96, 238, 0.8)');
   incomeGradient.addColorStop(1, 'rgba(116, 96, 238, 0.2)');

   new Chart(ctx, {
    type: 'bar',
    data: {
     labels: <?php echo json_encode($dates); ?>,
     datasets: [
      {
       label: 'Daily Income',
       data: <?php echo json_encode($incomes); ?>,
       backgroundColor: incomeGradient,
       borderColor: 'rgba(116, 96, 238, 1)',
       borderWidth: 1,
       yAxisID: 'y-income',
       order: 1
      },
      {
       label: 'Number of Orders',
       data: <?php echo json_encode($orderCounts); ?>,
       type: 'bar',
       backgroundColor: 'rgba(46, 202, 106, 0.6)',
       borderColor: 'rgba(46, 202, 106, 1)',
       borderWidth: 1,
       yAxisID: 'y-orders',
       order: 2
      },
      {
       label: 'Average Order Value',
       data: <?php echo json_encode($avgOrderValues); ?>,
       type: 'line',
       borderColor: 'rgba(255, 99, 132, 1)',
       borderWidth: 2,
       fill: false,
       yAxisID: 'y-income',
       pointBackgroundColor: 'rgba(255, 99, 132, 1)',
       pointRadius: 4,
       order: 0,
       spanGaps: true // This will skip null values instead of showing them as 0
      }
     ]
    },
    options: {
     responsive: true,
     maintainAspectRatio: false,
     interaction: {
      mode: 'index',
      intersect: false,
     },
     scales: {
      'y-income': {
       type: 'linear',
       position: 'left',
       title: {
        display: true,
        text: 'Income ($)',
        color: 'rgba(116, 96, 238, 1)'
       },
       beginAtZero: true,
       grid: {
        drawOnChartArea: true
       },
       ticks: {
        callback: function (value) {
         return '$' + value.toLocaleString(undefined, {
          maximumFractionDigits: 2
         });
        }
       }
      },
      'y-orders': {
       type: 'linear',
       position: 'right',
       title: {
        display: true,
        text: 'Number of Orders',
        color: 'rgba(46, 202, 106, 1)'
       },
       beginAtZero: true,
       grid: {
        drawOnChartArea: false
       }
      },
      x: {
       title: {
        display: true,
        text: 'Date'
       }
      }
     },
     plugins: {
      tooltip: {
       callbacks: {
        label: function (context) {
         let label = context.dataset.label || '';
         if (label) {
          label += ': ';
         }
         switch (context.dataset.label) {
          case 'Daily Income':
           return label + '$' + context.parsed.y.toLocaleString(undefined, {
            maximumFractionDigits: 2
           });
          case 'Number of Orders':
           if (context.raw === null) {
            return label + 'No orders';
           }
           if (context.raw === 1) {
            return label + context.parsed.y + ' order';
           }
           return label + context.parsed.y + ' orders';
          case 'Average Order Value':
           if (context.raw === null) {
            return label + 'No orders';
           }
           return label + '$' + context.parsed.y.toLocaleString(undefined, {
            maximumFractionDigits: 2
           });
          default:
           return label + context.parsed.y;
         }
        },
        afterBody: function (tooltipItems) {
         const idx = tooltipItems[0].dataIndex;
         const avgValue = <?php echo json_encode($avgOrderValues); ?>[idx];
         const highValue = <?php echo json_encode($highestOrders); ?>[idx];
         const lowValue = <?php echo json_encode($lowestOrders); ?>[idx];

         if (avgValue === null) {
          return ['', 'No orders on this day'];
         }

         return [
          '',
          'Daily Statistics:',
          'Average Order: $' + avgValue.toLocaleString(undefined, {
           maximumFractionDigits: 2
          }),
          'Highest Order: $' + highValue.toLocaleString(undefined, {
           maximumFractionDigits: 2
          }),
          'Lowest Order: $' + lowValue.toLocaleString(undefined, {
           maximumFractionDigits: 2
          })
         ];
        }
       }
      }
     }
    }
   });

   // Add event listener to the filter form submission
   const filterForm = document.getElementById('filterForm');
   const monthYearPicker = document.getElementById('monthYearPicker');
   const monthInput = document.getElementById('monthInput');
   const yearInput = document.getElementById('yearInput');

   filterForm.addEventListener('submit', function (e) {
    const [year, month] = monthYearPicker.value.split('-');
    monthInput.value = month;
    yearInput.value = year;
   });

  });
 });
</script>

<style>
 .card-stats {
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
 }

 .card-stats .card-body {
  padding: 15px;
 }

 .card-stats .card-title {
  color: #8898aa;
  font-size: 0.875rem;
  text-transform: uppercase;
  margin-bottom: 0.5rem;
 }

 .card-stats .card-value {
  color: #32325d;
  margin-bottom: 0;
 }

 /* Hide calendar icon in some browsers */
 input[type="month"]::-webkit-calendar-picker-indicator {
  cursor: pointer;
 }

 /* Ensure consistent height across browsers */
 input[type="month"] {
  height: calc(1.5em + .75rem + 2px);
 }

 /* Improve modal appearance on mobile */
 @media (max-width: 576px) {
  .modal-dialog {
   margin: 0.5rem;
  }

  input[type="month"] {
   font-size: 16px;
   /* Prevent zoom on mobile */
  }
 }

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