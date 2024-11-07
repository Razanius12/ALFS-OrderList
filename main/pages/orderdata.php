<?php

// Database connection
require_once 'config/database.php';

// Fetch orders from database
$query = "SELECT o.*, a.name_admin as project_manager 
          FROM orders o 
          JOIN admins a ON o.project_manager_id = a.id_admin 
          ORDER BY o.created_at DESC";
$result = mysqli_query($conn, $query);

// Helper function to determine badge class based on status
function getStatusBadgeClass($status) {
 switch (strtoupper($status)) {
     case 'COMPLETED': return 'success';
     case 'IN_PROGRESS': return 'info';
     case 'PENDING': return 'warning';
     case 'CANCELLED': return 'danger';
     default: return 'secondary';
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
          <th>Order ID</th>
          <th>Project Name</th>
          <th>Client</th>
          <th>Project Manager</th>
          <th>Start Date</th>
          <th>Status</th>
          <th>Workers</th>
          <th style="width: 10%">Action</th>
         </tr>
        </thead>
        <tbody>
         <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <tr>
           <td><?= htmlspecialchars($order['id_order']) ?></td>
           <td><?= htmlspecialchars($order['order_name']) ?></td>
           <td><?= htmlspecialchars($order['client_name']) ?></td>
           <td><?= htmlspecialchars($order['project_manager']) ?></td>
           <td><?= htmlspecialchars($order['start_date']) ?></td>
           <td>
            <span class="badge bg-<?= getStatusBadgeClass($order['project_status']) ?>">
             <?= htmlspecialchars($order['project_status']) ?>
            </span>
           </td>
           <td><?= htmlspecialchars($order['assigned_workers']) ?></td>
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
       <div class="row">
        <div class="col-md-6">
         <div class="form-group">
          <label>Project Name</label>
          <input type="text" class="form-control" name="order_name" required>
         </div>
        </div>
        <div class="col-md-6">
         <div class="form-group">
          <label>Client Name</label>
          <input type="text" class="form-control" name="client_name" required>
         </div>
        </div>
       </div>
       <div class="row mt-3">
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
           ?>
          </select>
         </div>
        </div>
        <div class="col-md-6">
         <div class="form-group">
          <label>Start Date</label>
          <input type="date" class="form-control" name="start_date" required>
         </div>
        </div>
       </div>
       <div class="row mt-3">
        <div class="col-md-6">
         <div class="form-group">
          <label>Project Status</label>
          <select class="form-control" name="project_status" required>
           <option value="PENDING">Pending</option>
           <option value="IN_PROGRESS">In Progress</option>
           <option value="COMPLETED">Completed</option>
           <option value="CANCELLED">Cancelled</option>
          </select>
         </div>
        </div>
        <div class="col-md-6">
         <div class="form-group">
          <label>Assigned Workers</label>
          <input type="number" class="form-control" name="assigned_workers" required min="1">
         </div>
        </div>
       </div>
       <div class="row mt-3">
        <div class="col-md-12">
         <div class="form-group">
          <label>Project Description</label>
          <textarea class="form-control" name="description" rows="3"></textarea>
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
       <div class="row">
        <div class="col-md-6">
         <div class="form-group">
          <label>Project Name</label>
          <input type="text" class="form-control" name="order_name" id="edit_order_name" required>
         </div>
        </div>
        <div class="col-md-6">
         <div class="form-group">
          <label>Client Name</label>
          <input type="text" class="form-control" name="client_name" id="edit_client_name" required>
         </div>
        </div>
       </div>
       <div class="row mt-3">
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
           ?>
          </select>
         </div>
        </div>
        <div class="col-md-6">
         <div class="form-group">
          <label>Start Date</label>
          <input type="date" class="form-control" name="start_date" id="edit_start_date" required>
         </div>
        </div>
       </div>
       <div class="row mt-3">
        <div class="col-md-6">
         <div class="form-group">
          <label>Project Status</label>
          <select class="form-control" name="project_status" id="edit_project_status" required>
           <option value="Pending">Pending</option>
           <option value="In Progress">In Progress</option>
           <option value="Completed">Completed</option>
           <option value="Overdue">Overdue</option>
          </select>
         </div>
        </div>
        <div class="col-md-6">
         <div class="form-group">
          <label>Assigned Workers</label>
          <input type="number" class="form-control" name="assigned_workers" id="edit_assigned_workers" required min="1">
         </div>
        </div>
       </div>
       <div class="row mt-3">
        <div class="col-md-12">
         <div class="form-group">
          <label>Project Description</label>
          <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
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
 $(document).ready(function () {
  // Initialize DataTable with multi-filter
  $('#multi-filter-select').DataTable({
   "pageLength": 10,
   initComplete: function () {
    this.api().columns().every(function () {
     var column = this;
     var select = $('<select class="form-control"><option value=""></option></select>')
      .appendTo($(column.footer()).empty())
      .on('change', function () {
       var val = $.fn.dataTable.util.escapeRegex(
        $(this).val()
       );
       column
        .search(val ? '^' + val + '$' : '', true, false)
        .draw();
      });

     column.data().unique().sort().each(function (d, j) {
      select.append('<option value="' + d + '">' + d + '</option>')
     });
    });
   }
  });

  // Add the new modal handlers here
  // Handle Add Order Form Submission
  $('#addOrderForm').on('submit', function (e) {
   e.preventDefault();
   const formData = new FormData(this);

   fetch('/api/addOrder.php', {
    method: 'POST',
    body: formData
   })
    .then(response => response.json())
    .then(data => {
     if (data.success) {
      Swal.fire({
       title: 'Success!',
       text: 'Order has been added successfully',
       icon: 'success'
      }).then(() => {
       location.reload();
      });
     } else {
      Swal.fire({
       title: 'Error!',
       text: data.message || 'Failed to add order',
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
  });

  // Handle Edit Order Modal Population
  $('#editOrderModal').on('show.bs.modal', function (event) {
   const button = $(event.relatedTarget);
   const orderId = button.data('order-id');

   // Fetch order details
   fetch(`/api/getOrder.php?id=${orderId}`)
    .then(response => response.json())
    .then(data => {
     if (data.success) {
      const order = data.order;
      $('#edit_order_id').val(order.id_order);
      $('#edit_order_name').val(order.order_name);
      $('#edit_client_name').val(order.client_name);
      $('#edit_project_manager_id').val(order.project_manager_id);
      $('#edit_start_date').val(order.start_date);
      $('#edit_project_status').val(order.project_status);
      $('#edit_assigned_workers').val(order.assigned_workers);
      $('#edit_description').val(order.description);
     }
    })
    .catch(error => {
     console.error('Error:', error);
     Swal.fire({
      title: 'Error!',
      text: 'Failed to load order details',
      icon: 'error'
     });
    });
  });

  // Handle Edit Order Form Submission
  $('#editOrderForm').on('submit', function (e) {
   e.preventDefault();
   const formData = new FormData(this);

   fetch('/api/updateOrder.php', {
    method: 'POST',
    body: formData
   })
    .then(response => response.json())
    .then(data => {
     if (data.success) {
      Swal.fire({
       title: 'Success!',
       text: 'Order has been updated successfully',
       icon: 'success'
      }).then(() => {
       location.reload();
      });
     } else {
      Swal.fire({
       title: 'Error!',
       text: data.message || 'Failed to update order',
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
  });
 });

 // Function to handle order deletion
 function deleteOrder(orderId) {
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
    fetch('/api/deleteOrder.php', {
     method: 'POST',
     headers: {
      'Content-Type': 'application/json',
     },
     body: JSON.stringify({ id: orderId })
    })
     .then(response => response.json())
     .then(data => {
      if (data.success) {
       Swal.fire(
        'Deleted!',
        'Order has been deleted.',
        'success'
       ).then(() => {
        location.reload();
       });
      } else {
       Swal.fire(
        'Error!',
        data.message || 'Failed to delete order',
        'error'
       );
      }
     })
     .catch(error => {
      console.error('Error:', error);
      Swal.fire(
       'Error!',
       'An unexpected error occurred',
       'error'
      );
     });
   }
  });
 }
</script>