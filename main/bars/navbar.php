<div class="main-header">
 <div class="main-header-logo">
  <!-- Logo Header -->
  <div class="logo-header" data-background-color="dark">
   <a href="index.php" class="logo">
    <img src="main/img/ALFLogoLightLandscape.webp" alt="ALF Solution Logo" class="navbar-brand" height="20">
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
    <li class="nav-item topbar-user dropdown hidden-caret">
     <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
      <div class="avatar-sm">
       <?php if (!empty($currentUser['profile_pic'])): ?>
        <div
         class="avatar-img-container rounded-circle bg-primary text-white d-flex align-items-center justify-content-center">
         <img src="<?= htmlspecialchars($currentUser['profile_pic']) ?>" class="avatar-img rounded-circle"
          alt="Profile Picture">
        </div>
       <?php else: ?>
        <div class="avatar-img rounded-circle bg-primary text-white d-flex align-items-center justify-content-center">
         <?= strtoupper(substr($currentUser['name'] ?? 'U', 0, 1)) ?>
        </div>
       <?php endif; ?>
      </div>
     </a>
     <ul class="dropdown-menu dropdown-user animated fadeIn">
      <div class="dropdown-user-scroll scrollbar-outer">
       <li>
        <div class="user-box">
         <div class="avatar-sm">
          <?php if (!empty($currentUser['profile_pic'])): ?>
           <div
            class="avatar-img-container rounded-circle bg-primary text-white d-flex align-items-center justify-content-center">
            <img src="<?= htmlspecialchars($currentUser['profile_pic']) ?>" class="avatar-img rounded-circle"
             alt="Profile Picture">
           </div>
          <?php else: ?>
           <div class="avatar-img rounded-circle bg-primary text-white d-flex align-items-center justify-content-center">
            <?= strtoupper(substr($currentUser['name'] ?? 'U', 0, 1)) ?>
           </div>
          <?php endif; ?>
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

<script src="main/js/logouts.js"></script>

<style>
 .avatar-img,
 .avatar-img-container {
  background-color: #f8f9fa;
  /* Light background color to show shadow */
  box-shadow: 0.08rem 0.08rem 0.05rem rgba(0, 0, 0, 0.2);
  /* Add shadow */
 }
</style>