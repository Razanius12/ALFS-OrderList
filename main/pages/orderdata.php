<?php

// Database connection
require_once 'config/database.php';

// Fetch orders from database
$query = "SELECT o.id_order, o.order_name, o.status, o.start_date, 
a.name_admin as project_manager,
GROUP_CONCAT(w.name_worker) as assigned_workers
FROM orders o
LEFT JOIN admins a ON o.project_manager_id = a.id_admin
LEFT JOIN project_assignments pa ON o.id_order = pa.id_order
LEFT JOIN workers w ON pa.id_worker = w.id_worker
GROUP BY o.id_order";
$result = mysqli_query($conn, $query);

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
     <a href="./index.php">
      <i class="icon-home"></i>
     </a>
    </li>
    <li class="separator">
     <i class="icon-arrow-right"></i>
    </li>
    <li class="nav-item">
     <a href="./index.php?page=orderData">Order Data</a>
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
          <th>Project Name</th>
          <th>Project Manager</th>
          <th>Worked By</th> <!-- this field is filled by a worker that working on the project -->
          <th>Start Date</th>
          <th>Status</th>
          <th style="width: 10%">Action</th>
         </tr>
        </thead>
        <tbody>
         <?php while ($order = mysqli_fetch_assoc($result)): ?>
          <tr>
           <td style="display: none;"><?= htmlspecialchars($order['id_order']) ?></td>
           <td><?= htmlspecialchars($order['order_name']) ?></td>
           <td><?= htmlspecialchars($order['project_manager']) ?></td>
           <td><?= htmlspecialchars($order['assigned_workers']) ?></td>
           <td><?= htmlspecialchars($order['start_date']) ?></td>
           </td>
           <td>
            <span class="badge bg-<?= getStatusBadgeClass($order['status']) ?>">
             <?= htmlspecialchars($order['status']) ?>
            </span>
           </td>
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
   <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
     <div class="modal-header">
      <h5 class="modal-title">Add New Order</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
     </div>
     <form id="addOrderForm" method="POST" action="/api/addOrder.php">
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
           <?php
           $query = "SELECT a.id_admin, a.name_admin 
            FROM admins a
            JOIN positions p ON a.id_position = p.id_position
            WHERE p.position_name LIKE '%manager%'
            AND p.department = 'ADMIN'";
           $adminOptions = mysqli_query($conn, $query);
           while ($admin = mysqli_fetch_assoc($adminOptions)): ?>
            <option value="<?= $admin['id_admin'] ?>"><?= $admin['name_admin'] ?></option>
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
          <label>Project Status</label>
          <select class="form-control" name="status" required>
           <option value="PENDING">Pending</option>
           <option value="IN_PROGRESS">In Progress</option>
           <option value="COMPLETED">Completed</option>
           <option value="CANCELLED">Cancelled</option>
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

  <!-- Edit Order Modal -->
  <div class="modal fade" id="editOrderModal" tabindex="-1" role="dialog" aria-hidden="true">
   <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
     <div class="modal-header">
      <h5 class="modal-title">Edit Order</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
     </div>
     <form id="#editOrderForm" method="POST" action="/api/updateOrder.php">
      <input type="hidden" name="id_order" id="edit_order_id">
      <div class="modal-body">
       <div class="row mt-3">
        <div class="col-md-6">
         <div class="form-group">
          <label>Project Name</label>
          <input type="text" class="form-control" name="order_name" id="edit_order_name" required>
         </div>
        </div>
        <div class="col-md-6">
         <div class="form-group">
          <label>Project Manager</label>
          <select class="form-control" name="project_manager_id" id="edit_project_manager_id" required>
           <?php
           $query = "SELECT a.id_admin, a.name_admin 
            FROM admins a
            JOIN positions p ON a.id_position = p.id_position
            WHERE p.position_name LIKE '%manager%'
            AND p.department = 'ADMIN'";
           $adminOptions = mysqli_query($conn, $query);
           while ($admin = mysqli_fetch_assoc($adminOptions)): ?>
            <option value="<?= $admin['id_admin'] ?>"><?= $admin['name_admin'] ?></option>
           <?php endwhile; ?>
          </select>
         </div>
        </div>
       </div>
       <div class="row mt-3">
        <div class="col-md-6">
         <div class="form-group">
          <label>Start Date</label>
          <input type="date" class="form-control" name="start_date" id="edit_start_date" required>
         </div>
        </div>
        <div class="col-md-6">
         <div class="form-group">
          <label>Project Status</label>
          <select class="form-control" name="status" id="edit_project_status" required>
           <option value="PENDING">Pending</option>
           <option value="IN_PROGRESS">In Progress</option>
           <option value="COMPLETED">Completed</option>
           <option value="CANCELLED">Cancelled</option>
          </select>
         </div>
        </div>
       </div>
       <div class="row mt-3">
        <div class="col-md-6">
         <div class="form-group">
          <label>Assign Worker</label>
          <select class="form-control" name="worker_id" id="edit_project_status" <?php echo ($status === 'PENDING') ? 'required' : ''; ?>>
           <option value="">Select Worker</option>
           <?php
           $query = "SELECT w.id_worker, w.name_worker, w.availability_status 
                FROM workers w
                JOIN positions p ON w.id_position = p.id_position
                WHERE w.availability_status = 'AVAILABLE'";
           $workerOptions = mysqli_query($conn, $query);
           while ($worker = mysqli_fetch_assoc($workerOptions)): ?>
            <option value="<?= $worker['id_worker'] ?>">
             <?= htmlspecialchars($worker['name_worker']) ?>
             (<?= htmlspecialchars($worker['availability_status']) ?>)
            </option>
           <?php endwhile; ?>
          </select>
         </div>
        </div>
        <div class="col-md-6">
         <div class="form-group">
          <label>Project Description</label>
          <textarea class="form-control" name="description" id="edit_description" rows="6"></textarea>
         </div>
        </div>
       </div>
      </div>
      <div class="modal-footer">
       <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
       <button type="submit" class="btn btn-primary">Save Changes</button>
      </div>
     </form>
    </div>
   </div>
  </div>

 </div>
</div>

<!-- Add necessary JavaScript -->
<script>
 // Initialize DataTable
 $(document).ready(function () {
  const orderTable = $('#multi-filter-select').DataTable({
   "order": [[0, "desc"]], // Sort by Order ID descending
   "columnDefs": [
    { "targets": [0], "visible": false } // Hide Order ID column
   ]
  });

  // Edit Order Modal Handler
  $('#editOrderModal').on('show.bs.modal', function (e) {
   const button = $(e.relatedTarget);
   const orderId = button.data('order-id');

   // Show loading state
   Swal.fire({
    title: 'Loading...',
    allowOutsideClick: false,
    didOpen: () => {
     Swal.showLoading();
    }
   });

   // Fetch order data
   $.ajax({
    url: '/api/getOrder.php',
    type: 'GET',
    data: { id_order: orderId },
    success: function (response) {
     const data = JSON.parse(response);

     // Populate form fields
     $('#edit_order_id').val(data.id_order);
     $('#edit_order_name').val(data.order_name);
     $('#edit_project_manager_id').val(data.project_manager_id);
     $('#edit_start_date').val(data.start_date);
     $('#edit_project_status').val(data.status);
     $('#edit_description').val(data.description);

     if (data.worker_id) {
      $('#edit_worker_id').val(data.worker_id);
     }

     Swal.close();
    },
    error: function () {
     Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Failed to fetch order data'
     });
    }
   });
  });

  // Delete Order Handler
  window.deleteOrder = function (orderId) {
   Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, delete it!'
   }).then((result) => {
    if (result.isConfirmed) {
     $.ajax({
      url: '/api/deleteOrder.php',
      type: 'POST',
      data: { id_order: orderId },
      success: function (response) {
       const data = JSON.parse(response);
       if (data.success) {
        Swal.fire(
         'Deleted!',
         'Order has been deleted.',
         'success'
        ).then(() => {
         // Reload the page or remove the row from table
         location.reload();
        });
       } else {
        Swal.fire(
         'Error!',
         data.message || 'Failed to delete order',
         'error'
        );
       }
      },
      error: function () {
       Swal.fire(
        'Error!',
        'Failed to delete order',
        'error'
       );
      }
     });
    }
   });
  };

  // Add Order Form Submit Handler
  $('#addOrderForm').on('submit', function (e) {
   e.preventDefault();

   Swal.fire({
    title: 'Adding Order...',
    allowOutsideClick: false,
    didOpen: () => {
     Swal.showLoading();
    }
   });

   $.ajax({
    url: $(this).attr('action'),
    type: 'POST',
    data: $(this).serialize(),
    success: function (response) {
     const data = JSON.parse(response);
     if (data.success) {
      Swal.fire({
       icon: 'success',
       title: 'Success',
       text: 'Order added successfully'
      }).then(() => {
       location.reload();
      });
     } else {
      Swal.fire({
       icon: 'error',
       title: 'Error',
       text: data.message || 'Failed to add order'
      });
     }
    },
    error: function () {
     Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Failed to add order'
     });
    }
   });
  });

  // Edit Order Form Submit Handler
  $('#editOrderForm').on('submit', function (e) {
   e.preventDefault();

   Swal.fire({
    title: 'Updating Order...',
    allowOutsideClick: false,
    didOpen: () => {
     Swal.showLoading();
    }
   });

   $.ajax({
    url: $(this).attr('action'),
    type: 'POST',
    data: $(this).serialize(),
    success: function (response) {
     const data = JSON.parse(response);
     if (data.success) {
      Swal.fire({
       icon: 'success',
       title: 'Success',
       text: 'Order updated successfully'
      }).then(() => {
       location.reload();
      });
     } else {
      Swal.fire({
       icon: 'error',
       title: 'Error',
       text: data.message || 'Failed to update order'
      });
     }
    },
    error: function () {
     Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Failed to update order'
     });
    }
   });
  });
 });
</script>