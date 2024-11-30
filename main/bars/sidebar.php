<?php
// Determine the current page
$currentPage = $_GET['page'] ?? 'dashboard';

// Define sidebar menu items
$mainSidebarItems = [
 'dashboard' => [
  'icon' => 'fas fa-home',
  'label' => 'Dashboard',
  'url' => './index.php?page=dashboard'
 ],
 'admins' => [
  'icon' => 'fas fa-users-cog',
  'label' => 'Admins',
  'url' => 'index.php?page=admins'
 ]
];

$contentSidebarItems = [
 'workers' => [
  'icon' => 'fas fa-pen-square',
  'label' => 'Workers',
  'url' => 'index.php?page=workers',
  'submenu' => ($currentPage === 'addNewPosition') ? [
   'addNewPosition' => [
    'icon' => 'fas fa-plus-circle',
    'label' => 'Add New Position',
    'url' => 'index.php?page=addNewPosition'
   ]
  ] : []
 ],
 'orderData' => [
  'icon' => 'fas fa-table',
  'label' => 'Order Data',
  'url' => 'index.php?page=orderData'
 ],
 'alfOffices' => [
  'icon' => 'fas fa-map-marker-alt',
  'label' => 'ALF Offices',
  'url' => 'index.php?page=alfOffices'
 ],
 'dailyProgress' => [
  'icon' => 'far fa-chart-bar',
  'label' => 'Daily Progress',
  'url' => 'index.php?page=dailyProgress'
 ]
];

// Function to render sidebar items with optional submenu
function renderSidebarItems($items, $currentPage)
{
 foreach ($items as $page => $item) {
  $isActive = ($currentPage === $page);
  $hasSubmenu = isset($item['submenu']) && !empty($item['submenu']);
  ?>
  <li class="nav-item <?= $isActive ? 'active' : '' ?>">
   <?php if ($hasSubmenu): ?>
    <div class="nav-link-wrapper">
     <a href="<?= $item['url'] ?>" class="nav-link">
      <i class="<?= $item['icon'] ?>"></i>
      <p><?= $item['label'] ?></p>
     </a>
     <a href="#<?= $page ?>Submenu" class="submenu-toggle" data-bs-toggle="collapse">
      <span class="caret"></span>
     </a>
    </div>
    <div class="collapse <?= ($currentPage === $page ||
     array_key_exists($currentPage, $item['submenu'])) ? 'show' : '' ?>" id="<?= $page ?>Submenu">
     <ul class="nav nav-collapse">
      <?php foreach ($item['submenu'] as $subPage => $subItem): ?>
       <li class="<?= ($currentPage === $subPage) ? 'active' : '' ?>">
        <a href="<?= $subItem['url'] ?>" style="
         padding-top: 0px !important;
         padding-right: 0px !important;
         padding-bottom: 0px !important;
         padding-left: 25px !important;
         margin-bottom: -10px !important;">
         <i class="<?= $subItem['icon'] ?>" style="margin-left: 25px;"></i>
         <p><?= $subItem['label'] ?></p>
        </a>
       </li>
      <?php endforeach; ?>
     </ul>
    </div>
   <?php else: ?>
    <a href="<?= $item['url'] ?>" class="nav-link">
     <i class="<?= $item['icon'] ?>"></i>
     <p><?= $item['label'] ?></p>
    </a>
   <?php endif; ?>
  </li>
  <?php
 }
}
?>

<!-- Sidebar -->
<div class="sidebar" data-background-color="dark">
 <?php include 'header.php'; ?>

 <div class="sidebar-wrapper scrollbar scrollbar-inner">
  <div class="sidebar-content">
   <ul class="nav nav-secondary">
    <!-- Main Sidebar Items -->
    <?php renderSidebarItems($mainSidebarItems, $currentPage); ?>

    <!-- Contents Section -->
    <li class="nav-section">
     <span class="sidebar-mini-icon">
      <i class="fa fa-ellipsis-h"></i>
     </span>
     <h4 class="text-section">Contents</h4>
    </li>
    <?php renderSidebarItems($contentSidebarItems, $currentPage); ?>

    <!-- Additional Links Section -->
    <li class="nav-section">
     <span class="sidebar-mini-icon">
      <i class="fa fa-ellipsis-h"></i>
     </span>
     <h4 class="text-section">Additional Links</h4>
    </li>

    <li class="nav-item">
     <a href="widgets.html">
      <i class="fas fa-desktop"></i>
      <p>Widgets</p>
      <span class="badge badge-success">4</span>
     </a>
    </li>
    <li class="nav-item">
     <a href="documentation/index.html">
      <i class="fas fa-file"></i>
      <p>Documentation</p>
      <span class="badge badge-secondary">1</span>
     </a>
    </li>
   </ul>
  </div>
 </div>
</div>
<!-- End Sidebar -->

<!-- Add some CSS to ensure proper submenu styling -->
<style>
 .nav-link-wrapper {
  display: flex;
  align-items: center;
  justify-content: space-between;
 }

 .nav-link-wrapper .nav-link {
  flex-grow: 1;
  margin-right: 10px;
 }

 .submenu-toggle {
  padding: 10px;
 }
</style>