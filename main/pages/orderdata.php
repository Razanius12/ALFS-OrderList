<?php

// Database connection (assuming you have a config file)
require_once 'config/database.php';


// Fetch orders from database
$query = "SELECT o.*, a.name_admin as project_manager 
          FROM orders o 
          JOIN admins a ON o.project_manager_id = a.id_admin 
          ORDER BY o.created_at DESC";
$result = mysqli_query($conn, $query);
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
      <h4 class="card-title">Orders Management</h4>
     </div>
     <div class="card-body">

      <div>
       <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOrderModal">
        <i class="fa fa-plus"></i> Add New Order
       </button>
      </div>

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
          <th>Amount</th>
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
           <td><?= number_format($order['total_amount'], 2) ?></td>
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
 });

 // Helper function to determine badge class based on status
 function getStatusBadgeClass(status) {
  switch (status.toLowerCase()) {
   case 'completed': return 'success';
   case 'in progress': return 'info';
   case 'pending': return 'warning';
   case 'overdue': return 'danger';
   default: return 'secondary';
  }
 }

 // Function to handle order deletion
 function deleteOrder(orderId) {
  if (confirm('Are you sure you want to delete this order?')) {
   fetch('api/deleteOrder.php', {
    method: 'POST',
    headers: {
     'Content-Type': 'application/json',
    },
    body: JSON.stringify({ id: orderId })
   })
    .then(response => response.json())
    .then(data => {
     if (data.success) {
      location.reload();
     } else {
      alert('Error deleting order: ' + data.message);
     }
    })
    .catch(error => {
     console.error('Error:', error);
     alert('Error deleting order');
    });
  }
 }
</script>