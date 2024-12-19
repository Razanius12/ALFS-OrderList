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
 $orderQuery = "SELECT COUNT(*) AS count FROM orders";
 $orderResult = mysqli_query($conn, $orderQuery);
 if ($orderResult) {
  $orderCount = mysqli_fetch_assoc($orderResult)['count'];
 }

 // Calculate total sales
 $totalSalesQuery = "SELECT SUM(order_price) AS total_sales FROM orders";
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
            SUM(order_price) as monthly_revenue
        FROM orders
        WHERE start_date >= DATE_SUB(CURRENT_DATE(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(start_date, '%Y-%m')
        ORDER BY month DESC
    ";
 $monthlyStatsResult = mysqli_query($conn, $monthlyStatsQuery);
 if ($monthlyStatsResult) {
  while ($row = mysqli_fetch_assoc($monthlyStatsResult)) {
   $monthlyStats[] = $row;
  }
 }

 // For workers, get their specific statistics
 if ($currentUser['role'] === 'worker') {
  $workerId = $currentUser['id'];
  $workerStatsQuery = "
            SELECT 
                COUNT(*) as total_tasks,
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

  <?php if ($currentUser['role'] === 'admin'): ?>
   <!-- Admin Dashboard -->
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
          <i class="fas fa-luggage-cart"></i>
         </div>
        </div>
        <div class="col col-stats ms-3 ms-sm-0">
         <div class="numbers">
          <p class="card-category">Total Sales</p>
          <h4 class="card-title">$ <?php echo htmlspecialchars(number_format($totalSales, 0, ',', '.')); ?></h4>
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
    <div class="col-sm-6 col-md-3">
     <div class="card card-stats card-round">
      <div class="card-body">
       <div class="row align-items-center">
        <div class="col-icon">
         <div class="icon-big text-center icon-warning bubble-shadow-small">
          <i class="fas fa-clock"></i>
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