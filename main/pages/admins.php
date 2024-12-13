<?php

adminOnlyPage();
$currentUser = getCurrentUserDetails();
checkPageAccess();

// Database connection
require 'config/database.php';

// Fetch admins from database
$query = "SELECT a.id_admin, a.username, a.name_admin, a.phone_number, a.password,
          p.position_name
          FROM admins a
          LEFT JOIN positions p ON a.id_position = p.id_position";
$result = mysqli_query($conn, $query);

// Check if the query was successful
if ($result === false) {
 // Log the error
 error_log("Database Query Error: " . mysqli_error($conn));
 $error_message = "Unable to retrieve admins. Please try again later.";
}

// Helper function to mask password
function maskPassword($password)
{
 return str_repeat('•', strlen($password));
}
?>
<!-- Page content -->
<div class="container">
 <div class="page-inner">

  <div class="page-header mb-0">
   <h3 class="fw-bold mb-3">Admins</h3>
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
     <a href="index.php?page=admins">Admins</a>
    </li>
   </ul>
  </div>

  <div class="row">

   <div class="col-md-12">
    <div class="card">
     <div class="card-header">
      <div class="d-flex align-items-center">
       <h4 class="card-title">Admins Management</h4>
       <button type="button" class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal"
        data-bs-target="#addAdminModal">
        <i class="fa fa-plus"></i> Add New Admins
       </button>
      </div>
     </div>
     <div class="card-body">
      <div class="table-responsive">
       <table id="admin-table" class="display table table-striped table-hover">
        <thead>
         <tr>
          <th data-orderable="true" style="display: none;">Admin ID</th>
          <th>Name</th>
          <th>Username</th>
          <th>Password</th>
          <th>Position</th>
          <th>Phone</th>
          <th style="width: 10%">Action</th>
         </tr>
        </thead>
        <tbody>
         <?php while ($admin = mysqli_fetch_assoc($result)): ?>
          <tr>
           <td style="display: none;"><?= htmlspecialchars($admin['id_admin']) ?></td>
           <td><?= htmlspecialchars($admin['name_admin']) ?></td>
           <td><?= htmlspecialchars($admin['username']) ?></td>
           <td>
            <div class="password-container">
             <span class="password-text" data-password="<?= htmlspecialchars($admin['password']) ?>">
              <?= maskPassword($admin['password']) ?>
             </span>
             <button class="btn btn-link btn-sm toggle-password" type="button">
              <i class="fa fa-eye"></i>
             </button>
            </div>
           </td>
           <td><?= htmlspecialchars($admin['position_name']) ?></td>
           <td><?= htmlspecialchars($admin['phone_number']) ?></td>
           <td>
            <div class="form-button-action">
             <button type="button" class="btn btn-link btn-primary btn-lg" data-bs-toggle="modal"
              data-bs-target="#editAdminModal" data-admin-id="<?= $admin['id_admin'] ?>">
              <i class="fa fa-edit"></i>
             </button>
             <button type="button" class="btn btn-link btn-danger" onclick="deleteAdmin(<?= $admin['id_admin'] ?>)">
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

 <!-- Add Admin Modal -->
 <div class="modal fade" id="addAdminModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
   <div class="modal-content">
    <div class="modal-header">
     <h5 class="modal-title">Add New Admin</h5>
     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <form id="addAdminForm" method="POST" action="main/api/addAdmin.php">
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
       <div class="col">
        <div class="form-group">
         <label>Full Name</label>
         <input type="text" class="form-control" name="name_admin" placeholder="Enter full name" required>
        </div>
       </div>
      </div>
      <div class="row mt-3">
       <div class="col-md-6">
        <div class="form-group">
         <label>Position</label>
         <div class="input-group">
          <select class="form-control position-select" name="id_position" data-department="ADMIN">
           <option value="">Select Position</option>
           <?php
           $query = "SELECT id_position, position_name 
                     FROM positions 
                     WHERE department = 'ADMIN'";
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
      <button type="submit" class="btn btn-primary">Add Admin</button>
     </div>
    </form>
   </div>
  </div>
 </div>

 <!-- Edit Admin Modal -->
 <div class="modal fade" id="editAdminModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
   <div class="modal-content">
    <div class="modal-header">
     <h5 class="modal-title">Edit Admin</h5>
     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <form id="editAdminForm" method="POST" action="main/api/updateAdmin.php">
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
       <div class="col">
        <div class="form-group">
         <label>Full Name</label>
         <input type="text" class="form-control" name="name_admin" id="edit_name_admin" required>
        </div>
       </div>
      </div>
      <div class="row mt-3">
       <div class="col-md-6">
        <div class="form-group">
         <label>Position</label>
         <div class="input-group">
          <select class="form-control position-select" name="id_position" id="edit_position" data-department="ADMIN">
           <option value="">Select Position</option>
           <?php
           $query = "SELECT id_position, position_name 
                     FROM positions 
                     WHERE department = 'ADMIN'";
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
       <div class="col-md-6">
        <div class="form-group">
         <label>Phone Number</label>
         <input type="tel" class="form-control" name="phone_number" id="edit_phone_number" required>
        </div>
       </div>
      </div>
     </div>
     <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      <button type="submit" class="btn btn-primary">Save Changes</button>
     </div>
     <input type="hidden" name="id_admin" id="edit_admin_id">
    </form>
   </div>
  </div>
 </div>
</div>

<!-- passwords css -->
<link rel="stylesheet" href="main/css/toggle.css" />

<script src="main/js/admin.js"></script>