<div class="main-header">
 <div class="main-header-logo">
  <!-- Logo Header -->
  <div class="logo-header" data-background-color="dark">
   <a href="index.php" class="logo">
    <img src="assets/img/kaiadmin/logo_light.svg" alt="navbar brand" class="navbar-brand" height="20" />
   </a>
   <div class="nav-toggle">
    <button class="btn btn-toggle toggle-sidebar">
     <i class="gg-menu-right"></i>
    </button>
    <button class="btn btn-toggle sidenav-toggler">
     <i class="gg-menu-left"></i>
    </button>
   </div>
   <button class="topbar-toggler more">
    <i class="gg-more-vertical-alt"></i>
   </button>
  </div>
  <!-- End Logo Header -->
 </div>
 <!-- Navbar Header -->
 <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
  <div class="container-fluid">

   <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
    <li class="nav-item topbar-icon dropdown hidden-caret">
     <a class="nav-link" data-bs-toggle="dropdown" href="#" aria-expanded="false">
      <i class="fas fa-layer-group"></i>
     </a>
     <div class="dropdown-menu quick-actions animated fadeIn">
      <div class="quick-actions-header">
       <span class="title mb-1">Quick Actions</span>
       <span class="subtitle op-7">Shortcuts</span>
      </div>
      <div class="quick-actions-scroll scrollbar-outer">
       <div class="quick-actions-items">
        <div class="row m-0">
         <a class="col-6 col-md-4 p-0" href="#">
          <div class="quick-actions-item">
           <div class="avatar-item bg-danger rounded-circle">
            <i class="far fa-calendar-alt"></i>
           </div>
           <span class="text">Calendar</span>
          </div>
         </a>
         <a class="col-6 col-md-4 p-0" href="#">
          <div class="quick-actions-item">
           <div class="avatar-item bg-warning rounded-circle">
            <i class="fas fa-map"></i>
           </div>
           <span class="text">Maps</span>
          </div>
         </a>
         <a class="col-6 col-md-4 p-0" href="#">
          <div class="quick-actions-item">
           <div class="avatar-item bg-info rounded-circle">
            <i class="fas fa-file-excel"></i>
           </div>
           <span class="text">Reports</span>
          </div>
         </a>
         <a class="col-6 col-md-4 p-0" href="#">
          <div class="quick-actions-item">
           <div class="avatar-item bg-success rounded-circle">
            <i class="fas fa-envelope"></i>
           </div>
           <span class="text">Emails</span>
          </div>
         </a>
         <a class="col-6 col-md-4 p-0" href="#">
          <div class="quick-actions-item">
           <div class="avatar-item bg-primary rounded-circle">
            <i class="fas fa-file-invoice-dollar"></i>
           </div>
           <span class="text">Invoice</span>
          </div>
         </a>
         <a class="col-6 col-md-4 p-0" href="#">
          <div class="quick-actions-item">
           <div class="avatar-item bg-secondary rounded-circle">
            <i class="fas fa-credit-card"></i>
           </div>
           <span class="text">Payments</span>
          </div>
         </a>
        </div>
       </div>
      </div>
     </div>
    </li>
    <script>
     console.log('Full Session Details:', <?php echo json_encode($_SESSION); ?>);
    </script>
    <li class="nav-item topbar-user dropdown hidden-caret">
     <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
      <div class="avatar-sm">
       <div class="avatar-img rounded-circle bg-primary text-white d-flex align-items-center justify-content-center">
        <?= strtoupper(substr($currentUser['name'] ?? 'U', 0, 1)) ?>
       </div>
      </div>
     </a>
     <ul class="dropdown-menu dropdown-user animated fadeIn">
      <div class="dropdown-user-scroll scrollbar-outer">
       <li>
        <div class="user-box">
         <div class="avatar-sm">
          <div class="avatar-img rounded-circle bg-primary text-white d-flex align-items-center justify-content-center">
           <?= strtoupper(substr($currentUser['name'] ?? 'U', 0, 1)) ?>
          </div>
         </div>
         <div class="u-text">
          <span class="op-7">Hi,</span>
          <span class="fw-bold"><?= htmlspecialchars($currentUser['name'] ?? 'User') ?></span>
          <p class="text-muted"><?= htmlspecialchars($currentUser['username'] ?? 'N/A') ?></p>
          <a href="index.php?page=profile" class="btn btn-xs btn-secondary btn-sm">View Profile</a>
         </div>
        </div>
       </li>
       <li>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="#" onclick="confirmLogout(); return false;" class="logout-link">
         <i class="fas fa-sign-out-alt"></i> Logout
        </a>
       </li>
      </div>
     </ul>
    </li>
   </ul>
  </div>
 </nav>
 <!-- End Navbar -->
</div>

<script src="main/js/logout.js"></script>