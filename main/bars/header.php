<div class="sidebar-logo">
 <div class="logo-header" data-background-color="dark">
  <a href="index.php" class="logo">
   <img src="main/img/ALFLogoLightLandscape.webp" alt="navbar brand" class="navbar-brand" height="20" />
  </a>
  <div class="nav-toggle d-flex align-items-center">
   <button class="btn btn-toggle toggle-sidebar p-2 me-1 btn-no-outline">
    <i class="gg-menu-right fs-4"></i>
   </button>
   <button class="btn btn-toggle sidenav-toggler p-2 me-1 btn-no-outline">
    <i class="gg-menu-left fs-4"></i>
   </button>
   <button class="topbar-toggler more p-2 btn-no-outline">
    <i class="gg-more-vertical-alt fs-4"></i>
   </button>
  </div>
 </div>
</div>

<style>
 .btn-no-outline {
  outline: none !important;
  box-shadow: none !important;
 }

 .btn-toggle {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 40px;
  border-radius: 50%;
  transition: background-color 0.3s ease;
  border: none;
 }

 .btn-toggle:focus,
 .btn-toggle:active {
  outline: none;
  box-shadow: none;
  background-color: transparent;
 }

 .btn-toggle:hover {
  background-color: rgba(255, 255, 255, 0.2);
 }

 .btn-toggle i {
  margin: 0;
 }
</style>