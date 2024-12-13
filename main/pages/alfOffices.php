<?php

sharedAccessPage();
$currentUser = getCurrentUserDetails();
checkPageAccess();

// Database connection
require 'config/database.php';

// Fetch ALF Offices data with error checking
$query = "SELECT * FROM gmaps ORDER BY name_city_district";
$result = mysqli_query($conn, $query);

// Check if the query was successful
if ($result === false) {
 // Log the error
 error_log("Database Query Error: " . mysqli_error($conn));
 $error_message = "Unable to retrieve offices. Please try again later.";
}
?>

<div class="container">
 <div class="page-inner">
  <div class="page-header mb-0">
   <h3 class="fw-bold mb-3">ALF Offices</h3>
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
     <a href="index.php?page=alfOffices">ALF Offices</a>
    </li>
   </ul>
   <?php if ($currentUser['role'] === 'admin'): ?>
    <button type="button" class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal"
     data-bs-target="#addAlfOffices">
     <i class="fa fa-plus"></i> Add Office
    </button>
   <?php endif; ?>
  </div>
  <div class="page-category">Shows all available ALF Solution Offices</div>
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
   } else if ($result && mysqli_num_rows($result) > 0) {
    // Loop through each office
    while ($office = mysqli_fetch_assoc($result)) {
     ?>
      <div class="col-md-12">
       <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
         <div class="card-title"><?php echo htmlspecialchars($office['name_city_district']); ?></div>
        <?php if ($currentUser['role'] === 'admin'): ?>
          <div class="card-tools">
           <a href="#" class="btn btn-sm btn-warning edit-office" data-bs-toggle="modal" data-bs-target="#editAlfOffices"
            data-id="<?php echo $office['id_maps']; ?>"
            data-city="<?php echo htmlspecialchars($office['name_city_district']); ?>"
            data-link="<?php echo htmlspecialchars($office['link_embed']); ?>">
            <i class="fa fa-edit"></i>
           </a>
           <a href="#" class="btn btn-sm btn-danger delete-office" data-id="<?php echo $office['id_maps']; ?>">
            <i class="fa fa-trash"></i>
           </a>
          </div>
        <?php endif; ?>
        </div>
        <div class="card-body">
         <?php
         // Ensure iframe is 100% width
         $embedMap = preg_replace(
          ['/(width=["\']\d+%?["\'])/i', '/(width=\d+)/i'],
          ['width="100%"', 'width="100%"'],
          $office['link_embed']
         );
         echo $embedMap;
         ?>
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
    <?php
   }

   // Close the database connection
   mysqli_close($conn);
   ?>
  </div>
 </div>
</div>

<!-- Modal for Adding New Office -->
<div class="modal fade" id="addAlfOffices" tabindex="-1" role="dialog" aria-hidden="true">
 <div class="modal-dialog modal-lg">
  <div class="modal-content">
   <div class="modal-header">
    <h5 class="modal-title">Add New Office</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
   </div>
   <form id="addAlfOfficesForm" method="POST" action="main/api/addAlfOffices.php">
    <div class="modal-body">
     <div class="row mt-3">
      <div class="col-md-6">
       <div class="form-group">
        <label>City/District</label>
        <input type="text" class="form-control" name="name_city_district" required>
       </div>
      </div>
      <div class="col-md-6">
       <div class="form-group">
        <label>Link Embed</label>
        <input type="text" class="form-control" name="link_embed" required>
        <small class="form-text text-muted">
         Enter full Google Maps embed iframe
        </small>
       </div>
      </div>
     </div>
    </div>
    <div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
     <button type="submit" class="btn btn-primary">Add Office</button>
    </div>
   </form>
  </div>
 </div>
</div>

<!-- Modal for Editing Office -->
<div class="modal fade" id="editAlfOffices" tabindex="-1" role="dialog" aria-hidden="true">
 <div class="modal-dialog modal-lg">
  <div class="modal-content">
   <div class="modal-header">
    <h5 class="modal-title">Edit Office</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
   </div>
   <form id="editAlfOffices" method="POST" action="main/api/updateAlfOffices.php">
    <div class="modal-body">
     <div class="row mt-3">
      <div class="col-md-6">
       <div class="form-group">
        <label>City/District</label>
        <input type="text" class="form-control" name="name_city_district" id="edit_name_city_district" required>
       </div>
      </div>
      <div class="col-md-6">
       <div class="form-group">
        <label>Link Embed</label>
        <input type="text" class="form-control" name="link_embed" id="edit_link_embed" required>
        <small class="form-text text-muted">
         Enter full Google Maps embed iframe
        </small>
       </div>
      </div>
     </div>
    </div>
    <div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
     <button type="submit" class="btn btn-primary">Save Changes</button>
    </div>
    <input type="hidden" name="id_maps" id="edit_id_maps">
   </form>
  </div>
 </div>
</div>

<script src="main/js/alfOffices.js"></script>

<script>
 // JavaScript untuk menangani modal edit
 document.addEventListener('DOMContentLoaded', function () {
  const editOfficeButtons = document.querySelectorAll('.edit-office');
  editOfficeButtons.forEach(button => {
   button.addEventListener('click', function () {
    const city = button.getAttribute('data-city');
    const link = button.getAttribute('data-link');
    const id = button.getAttribute('data-id');

    // Set nilai di modal edit
    document.getElementById('edit_name_city_district').value = city;
    document.getElementById('edit_id_maps').value = id;

    // Tampilkan peta Google Maps
    const imagePreview = document.getElementById('imagePreview');
    const iframe = document.createElement('iframe');
    iframe.src = link; // Asumsi link adalah URL embed Google Maps
    iframe.width = "600"; // Atur lebar iframe
    iframe.height = "450"; // Atur tinggi iframe
    iframe.style.border = "0"; // Hapus border
    iframe.allowFullscreen = true; // Izinkan fullscreen
    iframe.loading = "lazy"; // Lazy loading
    imagePreview.innerHTML = ""; // Kosongkan isi sebelumnya
    imagePreview.appendChild(iframe); // Tambahkan iframe ke dalam elemen preview
   });
  });
 });
</script>