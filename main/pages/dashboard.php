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
$totalSales = 0;
$monthlyStats = [];
$currentMonth = date('Y-m');

// Fetch counts and statistics
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

 // Count all orders
 $orderQuery = "SELECT COUNT(*) AS count FROM orders WHERE status = 'COMPLETED'";
 $orderResult = mysqli_query($conn, $orderQuery);
 if ($orderResult) {
  $orderCount = mysqli_fetch_assoc($orderResult)['count'];
 }

 // Calculate total sales
 $totalSalesQuery = "SELECT SUM(order_price) AS total_sales FROM orders WHERE status = 'COMPLETED'";
 $totalSalesResult = mysqli_query($conn, $totalSalesQuery);
 if ($totalSalesResult) {
  $totalSales = mysqli_fetch_assoc($totalSalesResult)['total_sales'];
 }

 // Monthly statistics - last 6 months
 $monthlyStatsQuery = "
        SELECT 
    DATE_FORMAT(start_date, '%Y-%m') as month,
    COUNT(*) as total_orders,
    COUNT(CASE WHEN status = 'COMPLETED' THEN 1 END) as completed_orders,
    SUM(CASE WHEN status = 'COMPLETED' THEN order_price ELSE 0 END) as monthly_revenue
FROM orders
WHERE start_date >= DATE_SUB(CURRENT_DATE(), INTERVAL 6 MONTH)
GROUP BY DATE_FORMAT(start_date, '%Y-%m')
ORDER BY month DESC;
    ";
 $monthlyStatsResult = mysqli_query($conn, $monthlyStatsQuery);
 if ($monthlyStatsResult) {
  while ($row = mysqli_fetch_assoc($monthlyStatsResult)) {
   $monthlyStats[] = $row;
  }
 }

 // Monthly totals for the current month
 $monthlyTotalsQuery = "
    SELECT 
        SUM(order_price) AS total_monthly_income,
        COUNT(*) AS total_monthly_orders,
        ROUND(AVG(order_price), 2) AS monthly_average_order
    FROM orders 
    WHERE status = 'COMPLETED' 
        AND MONTH(finished_at) = MONTH(CURRENT_DATE()) 
        AND YEAR(finished_at) = YEAR(CURRENT_DATE())
";

 $monthlyTotalsResult = mysqli_query($conn, $monthlyTotalsQuery);
 $monthlyTotals = mysqli_fetch_assoc($monthlyTotalsResult);

 $totalMonthlyIncome = $monthlyTotals['total_monthly_income'] ?? 0;
 $totalMonthlyOrders = $monthlyTotals['total_monthly_orders'] ?? 0;
 $averageMonthlyOrder = $monthlyTotals['monthly_average_order'] ?? 0;

 // Calculate Monthly Average Orders for Completed Orders
 $monthlyAverageOrdersQuery = "
 SELECT 
     ROUND(SUM(total_orders) / COUNT(DISTINCT CONCAT(year, '-', month)), 1) AS average_orders
 FROM (
     SELECT 
         MONTH(finished_at) AS month,
         YEAR(finished_at) AS year,
         COUNT(*) AS total_orders
     FROM orders 
     WHERE status = 'COMPLETED'
         AND finished_at IS NOT NULL
     GROUP BY YEAR(finished_at), MONTH(finished_at)
 ) AS monthly_orders
 ";

 $monthlyAverageOrdersResult = mysqli_query($conn, $monthlyAverageOrdersQuery);
 $monthlyAverageOrders = mysqli_fetch_assoc($monthlyAverageOrdersResult)['average_orders'] ?? 0;



 // For workers, get their specific statistics
 if ($currentUser['role'] === 'worker') {
  $workerId = $currentUser['id'];
  $workerStatsQuery = "
            SELECT 
                COUNT(*) as total_tasks,
                COUNT(CASE WHEN status = 'PENDING' THEN 1 END) as pending_tasks,
                COUNT(CASE WHEN status = 'COMPLETED' THEN 1 END) as completed_tasks,
                COUNT(CASE WHEN status = 'IN_PROGRESS' THEN 1 END) as ongoing_tasks
            FROM orders 
            WHERE worker_id = ?
        ";
  $stmt = mysqli_prepare($conn, $workerStatsQuery);
  mysqli_stmt_bind_param($stmt, "i", $workerId);
  mysqli_stmt_execute($stmt);
  $workerStats = mysqli_stmt_get_result($stmt)->fetch_assoc();
 }

} catch (Exception $e) {
 error_log("Dashboard Count Error: " . $e->getMessage());
}

// Get current worker's status
$workerId = $_SESSION['user_id'];
$statusQuery = "SELECT availability_status FROM workers WHERE id_worker = ?";
$stmt = mysqli_prepare($conn, $statusQuery);
mysqli_stmt_bind_param($stmt, "i", $workerId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$workerStatus = mysqli_fetch_assoc($result);

// Worker Performance Metrics
$workerId = $currentUser['id'];
$performanceQuery = "
    SELECT 
        COUNT(*) as total_completed_orders,
        AVG(TIMESTAMPDIFF(HOUR, start_date, finished_at)) as avg_hours_per_order,
        AVG(TIMESTAMPDIFF(HOUR, start_date, finished_at) / 
            TIMESTAMPDIFF(HOUR, start_date, deadline)) as efficiency_ratio,
        (SELECT AVG(TIMESTAMPDIFF(HOUR, start_date, finished_at)) 
         FROM orders 
         WHERE status = 'COMPLETED') as system_avg_hours
    FROM orders 
    WHERE worker_id = ? AND status = 'COMPLETED'
";

$stmt = mysqli_prepare($conn, $performanceQuery);
mysqli_stmt_bind_param($stmt, "i", $workerId);
mysqli_stmt_execute($stmt);
$performanceResult = mysqli_stmt_get_result($stmt);
$performance = mysqli_fetch_assoc($performanceResult);

// Calculate performance metrics
$avgHoursPerOrder = round($performance['avg_hours_per_order'], 2) ?? 0;
$systemAvgHours = round($performance['system_avg_hours'], 2) ?? 0;
$efficiencyRatio = round($performance['efficiency_ratio'] * 100, 2) ?? 0;
$totalCompletedOrders = $performance['total_completed_orders'] ?? 0;

// Recent Completed Task
$completedTasksQuery = "
    SELECT 
        o.id_order, 
        o.order_name, 
        o.start_date, 
        o.finished_at, 
        o.deadline,
        TIMESTAMPDIFF(DAY, o.start_date, o.finished_at) as days_taken,
        TIMESTAMPDIFF(DAY, o.finished_at, o.deadline) as days_before_deadline
    FROM 
        orders o
    WHERE 
        o.worker_id = ? AND o.status = 'COMPLETED'
    ORDER BY 
        o.finished_at DESC
    LIMIT 6
";

$stmt = mysqli_prepare($conn, $completedTasksQuery);
mysqli_stmt_bind_param($stmt, "i", $workerId);
mysqli_stmt_execute($stmt);
$completedTasksResult = mysqli_stmt_get_result($stmt);
$completedTasks = [];
while ($task = mysqli_fetch_assoc($completedTasksResult)) {
 $completedTasks[] = $task;
}

// Determine performance status
function getPerformanceStatus($efficiencyRatio)
{
 if ($efficiencyRatio > 100)
  return ['status' => 'Excellent', 'color' => 'success'];
 if ($efficiencyRatio > 80)
  return ['status' => 'Good', 'color' => 'info'];
 if ($efficiencyRatio > 60)
  return ['status' => 'Average', 'color' => 'warning'];
 return ['status' => 'Needs Improvement', 'color' => 'danger'];
}
$performanceStatus = getPerformanceStatus($efficiencyRatio);

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
     <a href="index.php?page=history" class="btn btn-primary btn-round me-2">Task History</a>
     <a href="index.php?page=task" class="btn btn-primary btn-round">My Tasks</a>
    </div>
   <?php endif; ?>
  </div>

  <?php if ($currentUser['role'] === 'admin'): ?>
   <!-- Admin Dashboard -->
   <div class="row">
    <div class="col-sm-6 col-md-4">
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
          <p class="card-category">Total Admins</p>
          <h4 class="card-title"><?php echo htmlspecialchars($adminCount); ?></h4>
         </div>
        </div>
       </div>
      </div>
     </div>
    </div>
    <div class="col-sm-6 col-md-4">
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
          <p class="card-category">Total Workers</p>
          <h4 class="card-title"><?php echo htmlspecialchars($workerCount); ?></h4>
         </div>
        </div>
       </div>
      </div>
     </div>
    </div>
    <div class="col-sm-6 col-md-4">
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
          <p class="card-category">Completed Orders</p>
          <h4 class="card-title"><?php echo htmlspecialchars($orderCount); ?></h4>
         </div>
        </div>
       </div>
      </div>
     </div>
    </div>
    <div class="col-sm-6 col-md-4">
     <div class="card card-stats card-round">
      <div class="card-body">
       <div class="row align-items-center">
        <div class="col-icon">
         <div class="icon-big text-center icon-warning bubble-shadow-small">
          <i class="fas fa-dollar-sign"></i>
         </div>
        </div>
        <div class="col col-stats ms-3 ms-sm-0">
         <div class="numbers">
          <p class="card-category">Monthly Income</p>
          <h4 class="card-title">$ <?php echo htmlspecialchars(number_format($totalMonthlyIncome, 2, ',', '.')); ?></h4>
         </div>
        </div>
       </div>
      </div>
     </div>
    </div>
    <div class="col-sm-6 col-md-4">
     <div class="card card-stats card-round">
      <div class="card-body">
       <div class="row align-items-center">
        <div class="col-icon">
         <div class="icon-big text-center icon-success bubble-shadow-small">
          <i class="fas fa-chart-line"></i>
         </div>
        </div>
        <div class="col col-stats ms-3 ms-sm-0">
         <div class="numbers">
          <p class="card-category">Avg Order Value</p>
          <h4 class="card-title">$ <?php echo htmlspecialchars(number_format($averageMonthlyOrder, 2, ',', '.')); ?></h4>
         </div>
        </div>
       </div>
      </div>
     </div>
    </div>
    <div class="col-sm-6 col-md-4">
     <div class="card card-stats card-round">
      <div class="card-body">
       <div class="row align-items-center">
        <div class="col-icon">
         <div class="icon-big text-center icon-warning bubble-shadow-small">
          <i class="fas fa-calendar-alt"></i>
         </div>
        </div>
        <div class="col col-stats ms-3 ms-sm-0">
         <div class="numbers">
          <p class="card-category">Avg Monthly Orders</p>
          <h4 class="card-title"><?php echo htmlspecialchars($monthlyAverageOrders); ?></h4>
         </div>
        </div>
       </div>
      </div>
     </div>
    </div>
   </div>

   <!-- Monthly Statistics for Admin -->
   <div class="row mt-4">
    <div class="col-md-12">
     <div class="card">
      <div class="card-header">
       <h4 class="card-title">Monthly Statistics (Last 6 Months)</h4>
      </div>
      <div class="card-body">
       <div class="table-responsive">
        <table class="table table-hover">
         <thead>
          <tr>
           <th>Month</th>
           <th>Total Orders</th>
           <th>Completed Orders</th>
           <th>Monthly Revenue</th>
          </tr>
         </thead>
         <tbody>
          <?php foreach ($monthlyStats as $stats): ?>
           <tr>
            <td><?php echo date('F Y', strtotime($stats['month'] . '-01')); ?></td>
            <td><?php echo htmlspecialchars($stats['total_orders']); ?></td>
            <td><?php echo htmlspecialchars($stats['completed_orders']); ?></td>
            <td>$ <?php echo htmlspecialchars(number_format($stats['monthly_revenue'], 0, ',', '.')); ?></td>
           </tr>
          <?php endforeach; ?>
         </tbody>
        </table>
       </div>
      </div>
     </div>
    </div>
   </div>

  <?php else: ?>
   <!-- Worker Dashboard -->
   <div class="row">
    <div class="col-sm-6 col-md-3">
     <div class="card card-stats card-round">
      <div class="card-body">
       <div class="row align-items-center">
        <div class="col-icon">
         <div class="icon-big text-center icon-warning bubble-shadow-small">
          <i class="fas fa-hourglass-half"></i>
         </div>
        </div>
        <div class="col col-stats ms-3 ms-sm-0">
         <div class="numbers">
          <p class="card-category">Pending Tasks</p>
          <h4 class="card-title"><?php echo htmlspecialchars($workerStats['pending_tasks']); ?></h4>
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
         <div class="icon-big text-center icon-warning bubble-shadow-small">
          <i class="fas fa-spinner fa-spin"></i>
         </div>
        </div>
        <div class="col col-stats ms-3 ms-sm-0">
         <div class="numbers">
          <p class="card-category">Ongoing Tasks</p>
          <h4 class="card-title"><?php echo htmlspecialchars($workerStats['ongoing_tasks']); ?></h4>
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
          <i class="fas fa-check-circle"></i>
         </div>
        </div>
        <div class="col col-stats ms-3 ms-sm-0">
         <div class="numbers">
          <p class="card-category">Completed Tasks</p>
          <h4 class="card-title"><?php echo htmlspecialchars($workerStats['completed_tasks']); ?></h4>
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
         <div class="icon-big text-center icon-primary bubble-shadow-small">
          <i class="fas fa-tasks"></i>
         </div>
        </div>
        <div class="col col-stats ms-3 ms-sm-0">
         <div class="numbers">
          <p class="card-category">Total Tasks</p>
          <h4 class="card-title"><?php echo htmlspecialchars($workerStats['total_tasks']); ?></h4>
         </div>
        </div>
       </div>
      </div>
     </div>
    </div>
    <div class="col-sm-6 col-md-7">
     <div class="card card-stats card-round">
      <div class="card-body">
       <div class="row align-items-center">
        <div class="col-icon">
         <div class="icon-big text-center icon-<?php echo $performanceStatus['color']; ?> bubble-shadow-small">
          <i class="fas fa-tachometer-alt"></i>
         </div>
        </div>
        <div class="col col-stats ms-3 ms-sm-0">
         <div class="numbers">
          <p class="card-title">Performance Metrics</p>
          <div class="row">
           <div class="col-8">
            <small>Avg Hours per Order</small>
            <h5><?php echo htmlspecialchars($avgHoursPerOrder); ?> hrs</h5>
           </div>
           <div class="col-8">
            <small>Efficiency Ratio</small>
            <h5><?php echo htmlspecialchars($efficiencyRatio); ?>%</h5>
           </div>
          </div>
          <div class="row mt-2">
           <div class="col-12">
            <small>Performance Status</small>
            <span class="badge bg-<?php echo $performanceStatus['color']; ?>">
             <?php echo htmlspecialchars($performanceStatus['status']); ?>
            </span>
           </div>
          </div>
         </div>
        </div>
       </div>
      </div>
     </div>
    </div>
    <div class="col-sm-6 col-md-5">
     <div class="card card-stats card-round">
      <div class="card-body">
       <div class="row align-items-center">
        <div class="col-icon">
         <div class="icon-big text-center 
         <?php
         // Dynamically set the icon class based on availability status
         echo $workerStatus['availability_status'] === 'AVAILABLE'
          ? 'icon-success'
          : 'icon-warning';
         ?> bubble-shadow-small" id="availabilityIcon">
          <i class="fas fa-user-check"></i>
         </div>
        </div>
        <div class="col col-stats ms-3 ms-sm-0">
         <div class="numbers">
          <p class="card-category">Availability Status</p>
          <h4 class="card-title" id="availabilityStatus">
           <?php echo htmlspecialchars($workerStatus['availability_status']); ?>
          </h4>
          <button id="toggleAvailability" class="btn btn-sm mt-2 <?php
          // Dynamically set the button class based on availability status
          echo $workerStatus['availability_status'] === 'AVAILABLE'
           ? 'btn-success'
           : 'btn-warning'; ?>">
           Toggle Status
          </button>
         </div>
        </div>
       </div>
      </div>
     </div>
    </div>
    <!-- Completed Tasks Section -->
    <div class="row mt-4">
     <div class="col-12">
      <h4 class="page-title">Recently Completed Tasks</h4>
     </div>
     <?php if (!empty($completedTasks)): ?>
      <?php foreach ($completedTasks as $task): ?>
       <div class="col-md-4">
        <div class="card card-task">
         <div class="card-header">
          <div class="card-title">
           <?php echo htmlspecialchars($task['order_name']); ?>
          </div>
          <div class="card-tools">
           <?php
           // Determine badge color based on completion time
           $badgeClass = $task['days_before_deadline'] > 0 ? 'badge-success' : ($task['days_before_deadline'] < 0 ? 'badge-danger' : 'badge-warning');
           ?>
           <span class="badge <?php echo $badgeClass; ?>">
            <?php
            if ($task['days_before_deadline'] > 0) {
             echo "Completed {$task['days_before_deadline']} days early";
            } elseif ($task['days_before_deadline'] < 0) {
             echo "Completed " . abs($task['days_before_deadline']) . " days late";
            } elseif ($task['days_before_deadline'] == 0) {
             echo "Completed at deadline";
            }
            ?>
           </span>
          </div>
         </div>
         <div class="card-body">
          <div class="row">
           <div class="col-6">
            <small class="text-muted">Started</small>
            <p><?php echo date('M d, Y', strtotime($task['start_date'])); ?></p>
           </div>
           <div class="col-6">
            <small class="text-muted">Completed</small>
            <p><?php echo date('M d, Y', strtotime($task['finished_at'])); ?></p>
           </div>
          </div>
          <div class="row mt-2">
           <div class="col-12">
            <small class="text-muted">Total Days Taken</small>
            <p><?php echo $task['days_taken']; ?> days</p>
           </div>
          </div>
         </div>
        </div>
       </div>
      <?php endforeach; ?>
     <?php else: ?>
      <div class="col-12">
       <div class="alert alert-info">
        No completed tasks to display.
       </div>
      </div>
     <?php endif; ?>
    </div>
   </div>
  <?php endif; ?>

  <div class="row">
   <div class="page-category">
    We are currently working on the website <br>
    In the meantime, enjoy the current available feature
   </div>
  </div>
 </div>
</div>

<?php mysqli_close($conn); ?>

<script>
 document.getElementById('toggleAvailability').addEventListener('click', function () {
  const currentStatus = document.getElementById('availabilityStatus').textContent.trim();
  const newStatus = currentStatus === 'AVAILABLE' ? 'TASKED' : 'AVAILABLE';

  Swal.fire({
   title: 'Change Availability Status?',
   text: `Are you sure you want to change your status to ${newStatus}?`,
   icon: 'question',
   showCancelButton: true,
   confirmButtonColor: '#3085d6',
   cancelButtonColor: '#d33',
   confirmButtonText: 'Yes, change it!'
  }).then((result) => {
   if (result.isConfirmed) {
    // Show loading state
    Swal.fire({
     title: 'Updating...',
     text: 'Please wait while we update your status',
     allowOutsideClick: false,
     allowEscapeKey: false,
     didOpen: () => {
      Swal.showLoading();
     }
    });

    // Make the API call
    fetch('main/api/toggleAvailability.php', {
     method: 'POST',
     headers: {
      'Content-Type': 'application/json',
     },
     body: JSON.stringify({ currentStatus }) // Send the current status if needed
    })
     .then(response => response.json())
     .then(data => {
      if (data.success) {
       const iconElement = document.getElementById('availabilityIcon');
       const statusElement = document.getElementById('availabilityStatus');
       const buttonElement = document.getElementById('toggleAvailability');

       // Update the status text
       statusElement.textContent = data.new_status;

       // Toggle button color
       if (data.new_status === 'AVAILABLE') {
        buttonElement.classList.remove('btn-warning');
        buttonElement.classList.add('btn-success');
        iconElement.classList.remove('icon-warning'); // Change to appropriate class
        iconElement.classList.add('icon-success'); // Change to appropriate class
       } else {
        buttonElement.classList.remove('btn-success');
        buttonElement.classList.add('btn-warning');
        iconElement.classList.remove('icon-success'); // Change to appropriate class
        iconElement.classList.add('icon-warning'); // Change to appropriate class
       }

       // Show success message
       Swal.fire({
        title: 'Status Updated!',
        text: `Your status has been changed to ${data.new_status}`,
        icon: 'success',
       });
      } else {
       // Show error message
       Swal.fire({
        title: 'Error!',
        text: data.message || 'Failed to update status',
        icon: 'error'
       });
      }
     })
     .catch(error => {
      console.error('Error:', error);
      Swal.fire({
       title: 'Error!',
       text: 'An unexpected error occurred',
       icon: 'error'
      });
     });
   }
  });
 });
</script>