<?php
// Database connection
require 'config/database.php';

// Fetch ALF Offices data with error checking
$query = "SELECT * FROM gmaps ORDER BY name_city_district";
$result = mysqli_query($conn, $query);

// Check if the query was successful
if ($result === false) {
 // Log the error
 error_log("Database Query Error: " . mysqli_error($conn));

 // Optionally, you can display a user-friendly error message
 $error_message = "Unable to retrieve offices. Please try again later.";
}
?>

<div class="container">
 <div class="page-inner">
  <div class="page-header mb-0">
   <h3 class="fw-bold mb-3">ALF Offices</h3>
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
     <a href="./index.php?page=alfOffices">ALF Offices</a>
    </li>
   </ul>
   <button type="button" class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal"
    data-bs-target="#addAlfOffices">
    <i class="fa fa-plus"></i> Add tambah tempat
   </button>
  </div>
  <div class="page-category">Shows all available ALF Solution offices</div>
  <div class="row">
   <?php
   // Check if there was a query error
   if (isset($error_message)) {
    ?>
    <div class="col-md-12">
     <div class="alert alert-danger text-center">
      <?php echo htmlspecialchars($error_message); ?>
     </div>
    </div>
    <?php
   }
   // If no error, proceed with displaying results
   else if ($result && mysqli_num_rows($result) > 0) {
    // Loop through each office
    while ($office = mysqli_fetch_assoc($result)) {
     ?>
      <div class="col-md-12">
       <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
         <div class="card-title"><?php echo htmlspecialchars($office['name_city_district']); ?></div>
         <div class="card-tools">
          <a href="#" class="btn btn-sm btn-warning edit-office" data-bs-toggle="modal"
          data-bs-target="#editAlfOffices" data-id="<?php echo $office['id_maps']; ?>"
           data-city="<?php echo htmlspecialchars($office['name_city_district']); ?>"
           data-link="<?php echo htmlspecialchars($office['link_embed']); ?>">
           <i class="fa fa-edit"></i>
          </a>
          <a href="#" class="btn btn-sm btn-danger delete-office" data-id="<?php echo $office['id_maps']; ?>">
           <i class="fa fa-trash"></i>
          </a>
         </div>
        </div>
        <div class="card-body">
        <?php echo $office['link_embed']; ?>
        </div>
       </div>
      </div>
     <?php
    }
   } else {
    ?>
     <div class="col-md-12">
      <div class="alert alert-info text-center">
       No ALF Offices found. Add a new office using the button above.
      </div>
     </div>
   <?php }

   // Close the database connection
   mysqli_close($conn);
   ?>
  </div>
 </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addAlfOffices" tabindex="-1" role="dialog" aria-hidden="true">
 <div class="modal-dialog modal-lg">
  <div class="modal-content">
   <div class="modal-header">
    <h5 class="modal-title">Add New Admin</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
   </div>
   <form id="addAlfOffices" method="POST" action="main/api/addAlfOffices.php">
    <div class="modal-body">
     <div class="row mt-3">
      <div class="col-md-6">
       <div class="form-group">
        <label>nama kota</label>
        <input type="text" class="form-control" name="name_city_district" required>
       </div>
      </div>
      <div class="col-md-6">
       <div class="form-group">
        <label>masukan url lokasi </label>
        <input type="text" class="form-control" name="link_embed" required>
       </div>
      </div>
     </div>
     <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      <button type="submit" class="btn btn-primary">Add</button>
     </div>
    </div>
   </form>
  </div>
 </div>
</div>

<div class="modal fade" id="editAlfOffices" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
   <div class="modal-content">
    <div class="modal-header">
     <h5 class="modal-title">Edit Admin</h5>
     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <form id="editAlfOffices" method="POST" action="main/api/updateAdmin.php">
     <div class="modal-body">
      <div class="row mt-3">
       <div class="col-md-6">
        <div class="form-group">
         <label>nama kota</label>
         <input type="text" class="form-control" name="nama kota" id="name_city_district" required>
        </div>
       </div>
       <div class="col-md-6">
        <div class="form-group">
         <label>masukan Url lokasi </label>
         <input type="text" class="form-control" name="masukan url lokasi" id="link_embed" required>
        </div>
       </div>
      </div>
     </div>
     <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      <button type="submit" class="btn btn-primary">Save Changes</button>
     </div>
     <input type="hidden" name="name_city_district" id="id_maps">
    </form>
   </div>
  </div>
 </div>
<script src="main/js/alfOffices.js"></script>