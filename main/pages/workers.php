<?php

adminOnlyPage();
$currentUser = getCurrentUserDetails();
checkPageAccess();

// Database connection
require 'config/database.php';

// Fetch workers from database
$query = "
    SELECT 
        w.id_worker, 
        w.username, 
        w.name_worker, 
        w.gender_worker, 
        w.phone_number, 
        w.availability_status, 
        w.password,
        p.position_name,
        GROUP_CONCAT(o.order_name) AS assigned_orders,
        COALESCE(in_progress_counts.order_count, 0) AS order_count
    FROM 
        workers w
    LEFT JOIN 
        positions p ON w.id_position = p.id_position
    LEFT JOIN 
        orders o ON o.worker_id = w.id_worker
    LEFT JOIN (
        SELECT 
            worker_id,
            COUNT(id_order) AS order_count
        FROM 
            orders
        GROUP BY 
            worker_id
    ) AS in_progress_counts ON w.id_worker = in_progress_counts.worker_id
    GROUP BY 
        w.id_worker, 
        w.username, 
        w.name_worker, 
        w.gender_worker, 
        w.phone_number, 
        w.availability_status, 
        w.password,
        p.position_name
";
$result = mysqli_query($conn, $query);

// Check if the query was successful
if ($result === false) {
 // Log the error
 error_log("Database Query Error: " . mysqli_error($conn));
 $error_message = "Unable to retrieve workers. Please try again later.";
}

// Helper function to determine badge class based on availability
function getAvailabilityBadgeClass($status)
{
 return strtoupper($status) === 'AVAILABLE' ? 'success' : 'warning';
}

// Helper function to mask password
function maskPassword($password)
{
 return str_repeat('•', strlen($password));
}

?>

<div class="container">
 <div class="page-inner">

  <div class="page-header mb-0">
   <h3 class="fw-bold mb-3">Workers</h3>
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
     <a href="index.php?page=workers">Workers</a>
    </li>
   </ul>
  </div>

  <div class="row">

   <div class="col-md-12">
    <div class="card">
     <div class="card-header">
      <div class="d-flex align-items-center">
       <h4 class="card-title">Workers Management</h4>
       <button type="button" class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal"
        data-bs-target="#addWorkerModal">
        <i class="fa fa-plus"></i> Add New Worker
       </button>
      </div>
     </div>
     <div class="card-body">
      <div class="table-responsive">
       <table id="workers-table" class="display table table-striped table-hover">
        <thead>
         <tr>
          <th data-orderable="true" style="display: none;">Worker ID</th>
          <th>Name</th>
          <th>Username</th>
          <th>Password</th>
          <th>Position</th>
          <th>Gender</th>
          <th>Phone</th>
          <th>Related Orders Count</th>
          <th>Status</th>
          <th style="width: 10%">Action</th>
         </tr>
        </thead>
        <tbody>
         <?php while ($worker = mysqli_fetch_assoc($result)): ?>
          <tr>
           <td style="display: none;"><?= htmlspecialchars($worker['id_worker']) ?></td>
           <td><?= htmlspecialchars($worker['name_worker']) ?></td>
           <td><?= htmlspecialchars($worker['username']) ?></td>
           <td>
            <div class="password-container">
             <span class="password-text" data-password="<?= htmlspecialchars($worker['password']) ?>">
              <?= maskPassword($worker['password']) ?>
             </span>
             <button class="btn btn-link btn-sm toggle-password" type="button">
              <i class="fa fa-eye"></i>
             </button>
            </div>
           </td>
           <td><?= htmlspecialchars($worker['position_name']) ?></td>
           <td><?= htmlspecialchars($worker['gender_worker']) ?></td>
           <td><?= htmlspecialchars($worker['phone_number']) ?></td>
           <td><?= htmlspecialchars($worker['order_count']) ?></td>
           <td>
            <span class="badge bg-<?= getAvailabilityBadgeClass($worker['availability_status']) ?>">
             <?= htmlspecialchars($worker['availability_status']) ?>
            </span>
           </td>
           <td>
            <div class="form-button-action">
             <button type="button" class="btn btn-link btn-primary btn-lg" data-bs-toggle="modal"
              data-bs-target="#editWorkerModal" data-worker-id="<?= $worker['id_worker'] ?>">
              <i class="fa fa-edit"></i>
             </button>
             <button type="button" class="btn btn-link btn-danger" onclick="deleteWorker(<?= $worker['id_worker'] ?>)">
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

 <!-- Add Worker Modal -->
 <div class="modal fade" id="addWorkerModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
   <div class="modal-content">
    <div class="modal-header">
     <h5 class="modal-title">Add New Worker</h5>
     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <form id="addWorkerForm" method="POST" action="main/api/addWorker.php">
     <div class="modal-body">
      <div class="row mt-3">
       <div class="col-md-6">
        <div class="form-group">
         <label>Username</label>
         <div class="input-icon">
          <span class="input-icon-addon">
           <i class="fa fa-user"></i>
          </span>
          <input type="text" class="form-control" name="username" placeholder="Enter username" required>
         </div>
        </div>
       </div>
       <div class="col-md-6">
        <div class="form-group">
         <label>Password</label>
         <div class="input-group">
          <input type="password" class="form-control" name="password" placeholder="Enter password" required>
          <button class="btn btn-outline-secondary toggle-password" type="button">
           <i class="fa fa-eye"></i>
          </button>
         </div>
        </div>
       </div>
      </div>
      <div class="row mt-3">
       <div class="col-md-6">
        <div class="form-group">
         <label>Full Name</label>
         <input type="text" class="form-control" name="name_worker" placeholder="Enter full name" required>
        </div>
       </div>
       <div class="col-md-6">
        <div class="form-group">
         <label>Position</label>
         <div class="input-group">
          <select class="form-control position-select" name="id_position" data-department="WORKER">
           <option value="">Select Position</option>
           <?php
           $query = "SELECT id_position, position_name 
                     FROM positions 
                     WHERE department = 'WORKER'";
           $positionOptions = mysqli_query($conn, $query);
           while ($position = mysqli_fetch_assoc($positionOptions)): ?>
            <option value="<?= $position['id_position'] ?>">
             <?= $position['position_name'] ?>
            </option>
           <?php endwhile; ?>
          </select>
          <div class="input-group-append">
           <a href="index.php?page=addNewPosition" class="btn btn-outline-secondary">
            <i class="fas fa-plus"></i>
           </a>
          </div>
         </div>
        </div>
       </div>
      </div>
      <div class="row mt-3">
       <div class="col-md-6">
        <div class="form-group">
         <label>Gender</label>
         <select class="form-control" name="gender_worker" required>
          <option value="">Select Gender</option>
          <option value="MALE">Male</option>
          <option value="FEMALE">Female</option>
          <option value="OTHER">Other</option>
         </select>
        </div>
       </div>
       <div class="col-md-6">
        <div class="form-group">
         <label>Phone Number</label>
         <input type="tel" class="form-control" name="phone_number" placeholder="Enter phone number" required>
        </div>
       </div>
      </div>
     </div>
     <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      <button type="submit" class="btn btn-primary">Add Worker</button>
     </div>
    </form>
   </div>
  </div>
 </div>

 <!-- Edit Worker Modal -->
 <div class="modal fade" id="editWorkerModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
   <div class="modal-content">
    <div class="modal-header">
     <h5 class="modal-title">Edit Worker</h5>
     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <form id="editWorkerForm" method="POST" action="main/api/updateWorker.php">
     <input type="hidden" name="id_worker" id="edit_worker_id">
     <div class="modal-body">
      <div class="row mt-3">
       <div class="col-md-6">
        <div class="form-group">
         <label>Username</label>
         <input type="text" class="form-control" name="username" id="edit_username" required>
        </div>
       </div>
       <div class="col-md-6">
        <div class="form-group">
         <label>Password (leave as it is to keep current)</label>
         <div class="input-group">
          <input type="password" class="form-control" name="password" id="edit_password">
          <button class="btn btn-outline-secondary toggle-password" type="button">
           <i class="fa fa-eye"></i>
          </button>
         </div>
        </div>
       </div>
      </div>
      <div class="row mt-3">
       <div class="col-md-6">
        <div class="form-group">
         <label>Full Name</label>
         <input type="text" class="form-control" name="name_worker" id="edit_name_worker" required>
        </div>
       </div>
       <div class="col-md-6">
        <div class="form-group">
         <label>Position</label>
         <div class="input-group">
          <select class="form-control position-select" name="id_position" id="edit_position" data-department="WORKER">
           <option value="">Select Position</option>
           <?php
           $query = "SELECT id_position, position_name 
                     FROM positions 
                     WHERE department = 'WORKER'";
           $positionOptions = mysqli_query($conn, $query);
           while ($position = mysqli_fetch_assoc($positionOptions)): ?>
            <option value="<?= $position['id_position'] ?>">
             <?= $position['position_name'] ?>
            </option>
           <?php endwhile; ?>
          </select>
          <div class="input-group-append">
           <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#addPositionModal">
            <i class="fas fa-plus"></i>
           </button>
          </div>
         </div>
        </div>
       </div>
      </div>
      <div class="row mt-3">
       <div class="col-md-6">
        <div class="form-group">
         <label>Gender</label>
         <select class="form-control" name="gender_worker" id="edit_gender" required>
          <option value="MALE">Male</option>
          <option value="FEMALE">Female</option>
          <option value="OTHER">Other</option>
         </select>
        </div>
       </div>
       <div class="col-md-6">
        <div class="form-group">
         <label>Phone Number</label>
         <input type="tel" class="form-control" name="phone_number" id="edit_phone_number" required>
        </div>
       </div>
      </div>
      <div class="row mt-3">
       <div class="col-md-6">
        <div class="form-group">
         <label>&nbsp;</label>
         <button type="button" class="btn btn-primary" id="searchOrdersButton">
          Search Related Orders
         </button>
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

<!-- passwords css -->
<link rel="stylesheet" href="main/css/toggle.css" />

<script src="main/js/worker.js"></script>