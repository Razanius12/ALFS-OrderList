<?php
// Database connection
require_once 'config/database.php';

// Existing query to fetch orders
$query = "SELECT o.*, 
          a.name_admin AS project_manager
          FROM orders o
          LEFT JOIN admins a ON o.project_manager_id = a.id_admin";

// Execute query with error checking
$result = mysqli_query($conn, $query);

// Check if query was successful
if ($result === false) {
 // Query failed
 echo "Error executing query: " . mysqli_error($conn);
 exit;
}

// Worker dropdown query
$workerQuery = "SELECT id_worker, name_worker 
                FROM workers 
                WHERE availability_status = 'AVAILABLE'";
$workerOptions = mysqli_query($conn, $workerQuery);

// Project managers dropdown
$adminQuery = "SELECT a.id_admin, a.name_admin 
               FROM admins a
               JOIN positions p ON a.id_position = p.id_position";
$adminOptions = mysqli_query($conn, $adminQuery);

// Helper function to determine badge class based on status
function getStatusBadgeClass($status)
{
 switch (strtoupper($status)) {
  case 'COMPLETED':
   return 'success';
  case 'IN_PROGRESS':
   return 'info';
  case 'PENDING':
   return 'warning';
  case 'CANCELLED':
   return 'danger';
  default:
   return 'secondary';
 }
}
?>

<!-- HTML Content -->
<div class="container">
 <div class="page-inner">
  <div class="page-header mb-0">
   <h3 class="fw-bold mb-3">Order Data</h3>
   <ul class="breadcrumbs mb-3">
    <li class="nav-home">
     <a href="index.php">
      <i class="icon-home"></i>
     </a>
    </li>
    <li class="separator">
     <i class="icon-arrow-right"></i>
    </li>
    <li class="nav-item">
     <a href="index.php?page=orderData">Order Data</a>
    </li>
   </ul>
  </div>

  <div class="row">
   <div class="col-md-12">
    <div class="card">
     <div class="card-header">
      <div class="d-flex align-items-center">
       <h4 class="card-title">Orders Management</h4>
       <button type="button" class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal"
        data-bs-target="#addOrderModal">
        <i class="fa fa-plus"></i> Add New Order
       </button>
      </div>
     </div>
     <div class="card-body">
      <div class="table-responsive">
       <table id="multi-filter-select" class="display table table-striped table-hover">
        <thead>
         <tr>
          <th data-orderable="true" style="display: none;">Order ID</th>
          <th>Order Name</th>
          <th>Status</th>
          <th>Start Date</th>
          <th>Project Manager</th>
          <th>Assigned Workers</th>
          <th>Assignment Status</th>
          <th style="width: 10%">Action</th>
         </tr>
        </thead>
        <tbody>
         <?php
         while ($order = mysqli_fetch_assoc($result)): ?>
          <tr>
           <td style="display: none;"><?= htmlspecialchars($order['id_order']) ?></td>
           <td><?= htmlspecialchars($order['order_name']) ?></td>
           <td>
            <span class="badge bg-<?= getStatusBadgeClass($order['status']) ?>">
             <?= htmlspecialchars($order['status']) ?>
            </span>
           </td>
           <td><?= htmlspecialchars($order['start_date']) ?></td>
           <td><?= htmlspecialchars($order['project_manager'] ?? 'N/A') ?></td>
           <td><?= htmlspecialchars($order['assigned_workers'] ?? 'Unassigned') ?></td>
           <td><?= htmlspecialchars($order['assignment_status'] ?? 'N/A') ?></td>
           <td>
            <div class="form-button-action">
             <button type="button" class="btn btn-link btn-primary btn-lg" data-bs-toggle="modal"
              data-bs-target="#editOrderModal" data-order-id="<?= $order['id_order'] ?>">
              <i class="fa fa-edit"></i>
             </button>
             <button type="button" class="btn btn-link btn-danger" onclick="deleteOrder(<?= $order['id_order'] ?>)">
              <i class="fa fa-times"></i>
             </button>
            </div>
           </td>
          </tr>
         <?php endwhile; ?>
        </tbody>
       </table>
      </div>
     </div>
    </div>
   </div>
  </div>

  <!-- Add Order Modal -->
  <div class="modal fade" id="addOrderModal" tabindex="-1" role="dialog" aria-hidden="true">
   <div class="modal-dialog modal-lg">
    <div class="modal-content">
     <div class="modal-header">
      <h5 class="modal-title">Add New Order</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
     </div>
     <form id="addOrderForm" method="POST" action="main/api/addOrder.php">
      <div class="modal-body">
       <div class="row mt-3">
        <div class="col-md-6">
         <div class="form-group">
          <label>Project Name</label>
          <input type="text" class="form-control" name="order_name" required>
         </div>
        </div>
        <div class="col-md-6">
         <div class="form-group">
          <label>Project Manager</label>
          <select class="form-control" name="project_manager_id" required>
           <option value="">Select Project Manager</option>
           <?php while ($admin = mysqli_fetch_assoc($adminOptions)): ?>
            <option value="<?= $admin['id_admin'] ?>"><?= htmlspecialchars($admin['name_admin']) ?></option>
           <?php endwhile; ?>
          </select>
         </div>
        </div>
       </div>
       <div class="row mt-3">
        <div class="col-md-6">
         <div class="form-group">
          <label>Start Date</label>
          <input type="date" class="form-control" name="start_date" required>
         </div>
        </div>
        <div class="col-md-6">
         <div class="form-group">
          <label>Assign Worker</label>
          <select class="form-control" name="worker_id" required>
           <option value="">Select Worker</option>
           <?php while ($worker = mysqli_fetch_assoc($workerOptions)): ?>
            <option value="<?= $worker['id_worker'] ?>"><?= htmlspecialchars($worker['name_worker']) ?></option>
           <?php endwhile; ?>
          </select>
         </div>
        </div>
       </div>
       <div class="row mt-3">
        <div class="col-md-12">
         <div class="form-group">
          <label>Project Description</label>
          <textarea class="form-control" name="description" rows="6"></textarea>
         </div>
        </div>
       </div>
      </div>
      <div class="modal-footer">
       <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
       <button type="submit" class="btn btn-primary">Add Order</button>
      </div>
     </form>
    </div>
   </div>
  </div>



 </div>
</div>

<script src="main/js/orderData.js"></script>