<?php
adminOnlyPage();
$currentUser = getCurrentUserDetails();
checkPageAccess();

require 'config/database.php';

$selectedMonth = isset($_GET['month']) ? $_GET['month'] : date('m');
$selectedYear = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Query for worker statistics
// Update the query section
$query = "SELECT 
    w.id_worker,
    w.name_worker,
    COUNT(DISTINCT CASE WHEN o.status = 'COMPLETED' AND MONTH(o.finished_at) = ? AND YEAR(o.finished_at) = ? THEN o.id_order END) as completed_orders,
    COUNT(DISTINCT CASE WHEN o.status = 'PENDING' THEN o.id_order END) as pending_orders,
    COUNT(DISTINCT CASE WHEN o.status = 'IN_PROGRESS' THEN o.id_order END) as in_progress_orders,
    COUNT(DISTINCT CASE WHEN o.status = 'CANCELLED' THEN o.id_order END) as cancelled_orders,
    SUM(CASE WHEN o.status = 'COMPLETED' AND MONTH(o.finished_at) = ? AND YEAR(o.finished_at) = ? THEN o.order_price ELSE 0 END) as total_earnings
FROM 
    workers w
LEFT JOIN 
    orders o ON w.id_worker = o.worker_id 
GROUP BY 
    w.id_worker, w.name_worker
ORDER BY 
    total_earnings DESC";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "iiii", $selectedMonth, $selectedYear, $selectedMonth, $selectedYear);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

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
  </div>

  <div class="row">
   <div class="col-md-12">
    <div class="card">
     <div class="card-header">
      <div class="d-flex align-items-center">
       <h4 class="card-title">Worker Performance - <?php echo $months[$selectedMonth] . ' ' . $selectedYear; ?></h4>
       <button class="btn btn-primary btn-round ms-auto" data-toggle="modal" data-target="#filterModal">
        <i class="fas fa-filter"></i> Filter
       </button>
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
          $totalOrders = $row['completed_orders'] + $row['pending_orders'] +
           $row['in_progress_orders'] + $row['cancelled_orders'];

          $ratio = $totalOrders > 0 ? [
           'completed' => round(($row['completed_orders'] / $totalOrders) * 100),
           'pending' => round(($row['pending_orders'] / $totalOrders) * 100),
           'in_progress' => round(($row['in_progress_orders'] / $totalOrders) * 100),
           'cancelled' => round(($row['cancelled_orders'] / $totalOrders) * 100)
          ] : ['completed' => 0, 'pending' => 0, 'in_progress' => 0, 'cancelled' => 0];
          ?>
          <tr>
           <td><?= htmlspecialchars($row['name_worker']) ?></td>
           <td><?= $row['completed_orders'] ?></td>
           <td>
            <?php if ($totalOrders == 0): ?>
             <div class="text-muted">No orders yet</div>
            <?php else: ?>
             <div class="progress" style="height: 20px;">
              <div class="progress-bar bg-success" style="width: <?= $ratio['completed'] ?>%"
               title="Completed: <?= $row['completed_orders'] ?>">
               <?= $ratio['completed'] ?>%
              </div>
              <div class="progress-bar bg-warning" style="width: <?= $ratio['pending'] ?>%"
               title="Pending: <?= $row['pending_orders'] ?>">
               <?= $ratio['pending'] ?>%
              </div>
              <div class="progress-bar bg-info" style="width: <?= $ratio['in_progress'] ?>%"
               title="In Progress: <?= $row['in_progress_orders'] ?>">
               <?= $ratio['in_progress'] ?>%
              </div>
              <div class="progress-bar bg-danger" style="width: <?= $ratio['cancelled'] ?>%"
               title="Cancelled: <?= $row['cancelled_orders'] ?>">
               <?= $ratio['cancelled'] ?>%
              </div>
             </div>
            <?php endif; ?>
           </td>
           <td>$<?= number_format($row['total_earnings'], 2) ?></td>
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
   <form method="GET" action="">
    <div class="modal-body">
     <input type="hidden" name="page" value="monthlyRecap">
     <div class="form-group">
      <label>Select Month</label>
      <select name="month" class="form-control">
       <?php foreach ($months as $num => $name): ?>
        <option value="<?= $num ?>" <?= ($num == $selectedMonth) ? 'selected' : '' ?>>
         <?= $name ?>
        </option>
       <?php endforeach; ?>
      </select>
     </div>
     <div class="form-group">
      <label>Select Year</label>
      <select name="year" class="form-control">
       <?php foreach ($years as $year): ?>
        <option value="<?= $year ?>" <?= ($year == $selectedYear) ? 'selected' : '' ?>>
         <?= $year ?>
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

   // Filter form submission handler
   $('#filterForm').on('submit', function (e) {
    e.preventDefault();
    Swal.fire({
     title: 'Loading...',
     allowOutsideClick: false,
     didOpen: () => {
      Swal.showLoading();
     }
    });
    this.submit();
   });
  });

 });
</script>