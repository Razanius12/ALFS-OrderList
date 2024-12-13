<?php

sharedAccessPage();
$currentUser = getCurrentUserDetails();
checkPageAccess();

// Database connection
require 'config/database.php';

?>

<!-- Page Content -->
<div class="container">
 <div class="page-inner">
  <h3 class="fw-bold mb-3">User Profile</h3>
  <div class="row">
   <div class="col-md-12">
    <div class="card">
     <div class="card-header">
      <div class="d-flex align-items-center">
       <h4 class="card-title">Customize Your Profile</h4>
      </div>
     </div>
     <form id="editProfile" method="POST" action="main/api/updateProfile.php">

      <!-- the id is based on id just like in the login.php which can determine whether it's admin or worker -->
      <input type="hidden" name="id" id="editId">
      <div class="card-body">
       <div class="row mt-3 justify-content-center">
        <div class="col">
         <div class="form-group">
          <label class="d-block mb-2">Profile Picture</label>
          <div class="avatar avatar-xl mx-auto">
           <div
            class="avatar-img rounded-circle bg-primary text-white d-flex align-items-center justify-content-center">
            <?= strtoupper(substr($currentUser['name'] ?? 'U', 0, 1)) ?>
           </div>
          </div>
         </div>
        </div>
       </div>
       <div class="row mt-3">
        <div class="col">
         <div class="form-group">
          <label>Username</label>
          <input type="text" class="form-control" name="username" id="edit_username" required>
         </div>
        </div>
       </div>
       <div class="row mt-3">
        <div class="col">
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
          <input type="text" class="form-control" name="name_worker" id="edit_name_worker" required>
         </div>
        </div>
       </div>
       <div class="row mt-3">
        <div class="col">
         <div class="form-group">
          <label>Gender</label>
          <select class="form-control" name="gender_worker" id="edit_gender" required>
           <option value="MALE">Male</option>
           <option value="FEMALE">Female</option>
           <option value="OTHER">Other</option>
          </select>
         </div>
        </div>
       </div>
       <div class="row mt-3">
        <div class="col">
         <div class="form-group">
          <label>Phone Number</label>
          <input type="tel" class="form-control" name="phone_number" id="edit_phone_number" required>
         </div>
        </div>
       </div>
       <div class="text-end mt-3 mb-3">
        <button class="btn btn-danger">Reset</button>
        <button class="btn btn-success">Save</button>
       </div>
      </div>
     </form>
    </div>
   </div>
  </div>
 </div>
</div>