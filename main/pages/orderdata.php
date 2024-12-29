<?php

adminOnlyPage();
$currentUser = getCurrentUserDetails();
checkPageAccess();

// Database connection
require 'config/database.php';

// Existing query to fetch orders
$query = "SELECT 
           o.id_order,
           o.order_name,
           o.status,
           o.order_price,
           o.finished_at,
           o.deadline,
           a.name_admin AS project_manager,
           w.name_worker AS assigned_worker,
           w.availability_status AS worker_availability
          FROM 
           orders o
          LEFT JOIN 
           admins a ON o.project_manager_id = a.id_admin
          LEFT JOIN 
           workers w ON o.worker_id = w.id_worker
          ORDER BY 
           o.id_order DESC";
$result = mysqli_query($conn, $query);

// Check if the query was successful
if ($result === false) {
 // Log the error
 error_log("Database Query Error: " . mysqli_error($conn));
 $error_message = "Unable to retrieve orders. Please try again later.";
}

$workerQuery = "SELECT 
                id_worker, 
                name_worker, 
                availability_status 
                FROM workers";
$workerOptions = mysqli_query($conn, $workerQuery);

// Project managers dropdown
$adminQuery = "SELECT a.id_admin, a.name_admin 
               FROM admins a
               JOIN positions p ON a.id_position = p.id_position";
$adminOptions = mysqli_query($conn, $adminQuery);

// Get current user's ID and level from session
$currentUserId = $_SESSION['user_id'] ?? null;
$currentUserLevel = $_SESSION['level'] ?? null;

// Initialize variables for JavaScript
$autoSelectProjectManager = 'null';
$isAdmin = 'false';

// If user is an admin, prepare their ID for auto-selection
if ($currentUserLevel === 'admin') {
 $autoSelectProjectManager = $currentUserId;
 $isAdmin = 'true';
}

// Helper function to format order name
function formatOrderName($orderName)
{
 // Decode the string after escaping it with htmlspecialchars
 $decodedName = htmlspecialchars_decode($orderName, ENT_QUOTES);

 // Replace escaped characters
 $formattedName = str_replace(
  ['\\"', "\\'", '\\n', '\\r'],
  ['"', "'", "\n", ''],
  $decodedName
 );

 // Split by line breaks and join with <br>
 $formattedName = implode('<br>', explode("\n", $formattedName));

 return $formattedName;
}

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
       <table id="order-data-table" class="display table table-striped table-hover">
        <thead>
         <tr>
          <th data-orderable="true" style="display: none;">Order ID</th>
          <th>Order Name</th>
          <th>Remaining Time</th>
          <th>Finished At</th>
          <th>Project Manager</th>
          <th>Assigned To</th>
          <th>Status</th>
          <th>Price</th>
          <th style="width: 10%">Action</th>
         </tr>
        </thead>
        <tbody>
         <?php while ($order = mysqli_fetch_assoc($result)): ?>
          <tr>
           <td style="display: none;"><?= htmlspecialchars($order['id_order']) ?></td>
           <td><?= formatOrderName($order['order_name']) ?></td>
           <td>
            <?php
            // Format deadline
            $deadlineDate = new DateTime($order['deadline']);
            // Calculate remaining time
            $now = new DateTime();

            if (htmlspecialchars($order['status']) == 'COMPLETED') {
             echo '<span class="text-success">Completed</span>'; // Order completed
            } else if (htmlspecialchars($order['status']) == 'CANCELLED') {
             echo '<span class="text-danger">Cancelled</span>'; // Order cancelled
            } else {
             // Compare dates without time
             $nowDate = new DateTime($now->format('Y-m-d'));

             if ($nowDate > $deadlineDate) {
              // Overdue case
              echo '<span class="text-danger">Overdue</span>'; // Deadline passed
             } else if ($nowDate == $deadlineDate) {
              // Less than 1 day left (today is the deadline)
              echo '<span class="text-warning">Less than 1 day left</span>';
             } else {
              // Calculate days remaining
              $interval = $nowDate->diff($deadlineDate);
              $daysLeft = $interval->days;

              if ($daysLeft > 1) {
               // More than 1 day remaining
               $timeLeft = "$daysLeft days left";
               echo '<span class="text-success">' . htmlspecialchars($timeLeft) . '</span>';
              } else {
               // Less than 1 day left
               echo '<span class="text-warning">Less than 1 day left</span>';
              }
             }
            }
            ?>
           </td>
           <td>
            <?php
            // Convert datetime 
            if (!empty(htmlspecialchars($order['finished_at']))) {
             $finishedAT = new DateTime($order['finished_at']);
             echo $finishedAT->format('Y F d') . ' at ' . $finishedAT->format('h:i A');
            } else {
             echo 'Not finished';
            }
            ?>
           </td>
           <td><?= htmlspecialchars($order['project_manager'] ?? 'N/A') ?></td>
           <td>
            <?= $order['assigned_worker'] ? htmlspecialchars($order['assigned_worker']) : 'None' ?>
           </td>
           <td>
            <span class="badge bg-<?= getStatusBadgeClass($order['status']) ?>">
             <?= htmlspecialchars($order['status']) ?>
            </span>
           </td>
           <td>$ <?= number_format($order['order_price']) ?></td>
           <td>
            <div class="form-button-action">
             <button type="button" class="btn btn-link btn-primary btn-lg" data-bs-toggle="modal"
              data-bs-target="#editOrderModal" data-order-id="<?= $order['id_order'] ?>">
              <i class="fa fa-edit"></i>
             </button>
             <button type="button" class="btn btn-link btn-info" onclick="viewAttachments(<?= $order['id_order'] ?>)">
              <i class="fa fa-paperclip"></i>
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
   <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
     <div class="modal-header">
      <h5 class="modal-title">Add New Order</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
     </div>
     <form id="addOrderForm" method="POST" enctype="multipart/form-data" action="main/api/addOrder.php">
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
          <select class="form-control" name="project_manager_id" required <?= $currentUserLevel === 'admin' ? 'disabled' : '' ?>>
           <option value="">Select Project Manager</option>
           <?php
           mysqli_data_seek($adminOptions, 0);
           while ($admin = mysqli_fetch_assoc($adminOptions)):
            $selected = ($currentUserLevel === 'admin' && $currentUserId == $admin['id_admin']) ? 'selected' : '';
            ?>
            <option value="<?= $admin['id_admin'] ?>" <?= $selected ?>><?= htmlspecialchars($admin['name_admin']) ?>
            </option>
           <?php endwhile; ?>
          </select>
          <?php if ($currentUserLevel === 'admin'): ?>
           <input type="hidden" name="project_manager_id" value="<?= $currentUserId ?>">
          <?php endif; ?>
         </div>
        </div>
       </div>
       <div class="row mt-3">
        <div class="col-md-6">
         <div class="form-group">
          <label>Start Date</label>
          <input type="datetime-local" class="form-control" id="start_date" name="start_date">
         </div>
        </div>
        <div class="col-md-6">
         <div class="form-group">
          <label>Deadline</label>
          <input type="date" class="form-control" id="deadline" name="deadline">
         </div>
        </div>
       </div>
       <div class="row mt-3">
        <div class="col-md-6">
         <div class="form-group">
          <label>Order Price</label>
          <div class="input-icon">
           <span class="input-icon-addon">
            <i class="fas fa-dollar-sign"></i>
           </span>
           <input type="number" class="form-control" name="order_price">
          </div>
         </div>
        </div>
        <div class="col-md-6">
         <div class="form-group">
          <label>Assign Worker (Optional)</label>
          <select class="form-control" name="assigned_worker">
           <option value="">Select Worker</option>
           <?php
           // Reset the pointer to the beginning of the result set
           mysqli_data_seek($workerOptions, 0);
           while ($worker = mysqli_fetch_assoc($workerOptions)): ?>
            <option value="<?= $worker['id_worker'] ?>">
             <?= htmlspecialchars($worker['name_worker']) ?>
             (<?= $worker['availability_status'] ?>)
            </option>
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
       <div class="row mt-3">
        <div class="col-md-12">
         <div class="form-group">
          <label>Reference Files (Optional)</label>
          <input type="file" class="form-control" name="references[]" multiple
           accept=".jpg,.jpeg,.png,.gif,.svg,.ai,.psd,.cdr" max="10">
          <small class="text-muted">Allowed files: JPG, JPEG, SVG, GIF, WEBP, AI, PSD, CDR, PDF (Max 5MB each)</small>
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
  <?php
  mysqli_data_seek($adminOptions, 0);
  mysqli_data_seek($workerOptions, 0);
  ?>
  <div class="modal fade" id="editOrderModal" tabindex="-1" role="dialog" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
     <div class="modal-header">
      <h5 class="modal-title">Edit Order</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
     </div>
     <form id="editOrderForm" method="POST" action="main/api/updateOrder.php">
      <div class="modal-body">
       <input type="hidden" id="edit_order_id" name="order_id">
       <div class="row mt-3">
        <div class="col-md-6">
         <div class="form-group">
          <label>Project Name</label>
          <input type="text" class="form-control" id="edit_order_name" name="order_name"
           placeholder="Enter project name" required>
         </div>
        </div>
        <div class="col-md-6">
         <div class="form-group">
          <label>Project Manager</label>
          <select class="form-control" id="edit_project_manager" name="project_manager_id" required
           <?= $currentUserLevel === 'admin' ? 'disabled' : '' ?>>
           <option value="">Select Project Manager</option>
           <?php
           mysqli_data_seek($adminOptions, 0);
           while ($admin = mysqli_fetch_assoc($adminOptions)):
            $selected = ($currentUserLevel === 'admin' && $currentUserId == $admin['id_admin']) ? 'selected' : '';
            ?>
            <option value="<?= $admin['id_admin'] ?>" <?= $selected ?>><?= htmlspecialchars($admin['name_admin']) ?>
            </option>
           <?php endwhile; ?>
          </select>
          <?php if ($currentUserLevel === 'admin'): ?>
           <input type="hidden" name="project_manager_id" value="<?= $currentUserId ?>">
          <?php endif; ?>
         </div>
        </div>
       </div>
       <div class="row mt-3">
        <div class="col-md-6">
         <div class="form-group">
          <label>Start Date</label>
          <input type="datetime-local" class="form-control" id="edit_start_date" name="start_date" required>
         </div>
        </div>
        <div class="col-md-6">
         <div class="form-group">
          <label>Deadline</label>
          <input type="date" class="form-control" id="edit_deadline" name="deadline" required>
         </div>
        </div>
       </div>
       <div class="row mt-3">
        <div class="col-md-6">
         <div class="form-group">
          <label>Order Status</label>
          <select class="form-control" id="edit_status" name="status">
           <option value="">Select Order Status</option>
           <option value="PENDING">Pending</option>
           <option value="IN_PROGRESS">In Progress</option>
           <option value="COMPLETED">Completed</option>
           <option value="CANCELLED">Cancelled</option>
          </select>
         </div>
        </div>
        <div class="col-md-6">
         <div class="form-group">
          <label>Assign Worker (Optional)</label>
          <select class="form-control" id="edit_assigned_worker" name="assigned_worker">
           <option value="">Select Worker</option>
           <?php while ($worker = mysqli_fetch_assoc($workerOptions)): ?>
            <option value="<?= $worker['id_worker'] ?>">
             <?= htmlspecialchars($worker['name_worker']) ?>
             (<?= $worker['availability_status'] ?>)
            </option>
           <?php endwhile; ?>
          </select>
         </div>
        </div>
        <div class="row mt-3">
         <div class="col-md-12">
          <div class="form-group">
           <label>Order Price</label>
           <div class="input-icon">
            <span class="input-icon-addon">
             <i class="fas fa-dollar-sign"></i>
            </span>
            <input type="number" class="form-control" id="edit_order_price" name="order_price">
           </div>
          </div>
         </div>
        </div>
       </div>
       <div class="row mt-3">
        <div class="col-md-12">
         <div class="form-group">
          <label>Project Description (Optional)</label>
          <textarea class="form-control" id="edit_description" name="description" rows="4"></textarea>
         </div>
        </div>
       </div>
      </div>

      <div class="modal-footer">
       <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
       <button type="submit" class="btn btn-primary">Update Order</button>
      </div>
     </form>
    </div>
   </div>
  </div>

  <!-- View Attachments Modal -->
  <div class="modal fade" id="viewAttachmentsModal" tabindex="-1" role="dialog" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
     <div class="modal-header">
      <h5 class="modal-title" id="attachmentModalTitle">View Attachments</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
     </div>
     <div class="modal-body">
      <div class="row">
       <div class="col">
        <div class="form-group">
         <div class="description-section mb-4">
          <h6 class="fw-bold">Description:</h6>
          <p id="orderDescription">No description available</p>
         </div>
        </div>
       </div>
      </div>
      <div class="row mt-3">
       <div class="col">
        <div class="form-group">
         <h6 class="fw-bold">References:</h6>
         <div class="text-muted text-sm mb-2">(Click to see full preview)</div>
         <div id="referencesList" class="list-group"></div>
        </div>
       </div>
      </div>
      <div class="row mt-3 mb-3">
       <div class="col">
        <div class="form-group">
         <h6 class="fw-bold">Attachments:</h6>
         <div class="text-muted text-sm mb-2">(Click to see full preview)</div>
         <div id="attachmentsList" class="list-group"></div>
        </div>
       </div>
      </div>
     </div>
     <div class="modal-footer">

      <button type="button" class="btn btn-danger" onclick="deleteAllAttachments('all')">
       Delete All</button>
      <button type="button" class="btn btn-danger" onclick="deleteAllAttachments('references')">
       Delete All References</button>
      <button type="button" class="btn btn-danger" onclick="deleteAllAttachments('attachments')">
       Delete All Attachments</button>

      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
     </div>
    </div>
   </div>
  </div>

 </div>
</div>

<script src="main/js/orderdata.js"></script>

<script>
 // Make PHP session variables available to JavaScript
 window.isAdmin = <?= json_encode($currentUserLevel === 'admin') ?>;
 window.autoSelectProjectManager = <?= json_encode($currentUserId) ?>;
</script>