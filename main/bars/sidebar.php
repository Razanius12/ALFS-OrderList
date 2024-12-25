<?php
// Determine the current page
$currentPage = $_GET['page'] ?? 'dashboard';

// Define sidebar menu items with role-based access
$mainSidebarItems = [
 'dashboard' => [
  'icon' => 'fas fa-home',
  'label' => 'Dashboard',
  'url' => './index.php?page=dashboard',
  'roles' => ['admin', 'worker']
 ],
 'admins' => [
  'icon' => 'fas fa-users-cog',
  'label' => 'Admins',
  'url' => 'index.php?page=admins',
  'roles' => ['admin']
 ]
];

$contentSidebarItems = [
 'workers' => [
  'icon' => 'fas fa-pen-square',
  'label' => 'Workers',
  'url' => 'index.php?page=workers',
  'roles' => ['admin'],
  'submenu' => ($currentPage === 'addNewPosition') ? [
   'addNewPosition' => [
    'icon' => 'fas fa-plus-circle',
    'label' => 'Add New Position',
    'url' => 'index.php?page=addNewPosition',
    'roles' => ['admin']
   ]
  ] : []
 ],
 'task' => [
  'icon' => 'fas fa-tasks',
  'label' => 'My Tasks',
  'url' => 'index.php?page=task',
  'roles' => ['worker']
 ],
 'history' => [
  'icon' => 'fas fa-search',
  'label' => 'Task History',
  'url' => 'index.php?page=history',
  'roles' => ['worker']
 ],
 'orderData' => [
  'icon' => 'fas fa-table',
  'label' => 'Order Data',
  'url' => 'index.php?page=orderData',
  'roles' => ['admin']
 ],
 'alfOffices' => [
  'icon' => 'fas fa-map-marker-alt',
  'label' => 'ALF Offices',
  'url' => 'index.php?page=alfOffices',
  'roles' => ['admin', 'worker']
 ],
 'monthlyRecap' => [
  'icon' => 'far fa-chart-bar',
  'label' => 'Monthly Recap',
  'url' => 'index.php?page=monthlyRecap',
  'roles' => ['admin']
 ]
];

// Function to render sidebar items with optional submenu and role-based visibility
function renderSidebarItems($items, $currentPage, $currentUser)
{
 foreach ($items as $page => $item) {
  // Check if user has required role to see this item
  $hasAccess = isset($item['roles']) &&
   in_array($currentUser['role'], $item['roles']);

  if (!$hasAccess)
   continue;

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
      <?php
      if (isset($item['submenu'])) {
       foreach ($item['submenu'] as $subPage => $subItem):
        // Check submenu item role access
        $hasSubAccess = isset($subItem['roles']) &&
         in_array($currentUser['role'], $subItem['roles']);
        if (!$hasSubAccess)
         continue;
        ?>
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
        <?php
       endforeach;
      }
      ?>
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
 <?php include_once 'header.php'; ?>

 <div class="sidebar-wrapper scrollbar scrollbar-inner">
  <div class="sidebar-content">
   <ul class="nav nav-secondary">
    <!-- Main Sidebar Items -->
    <?php renderSidebarItems($mainSidebarItems, $currentPage, $currentUser); ?>

    <!-- Contents Section -->
    <li class="nav-section">
     <span class="sidebar-mini-icon">
      <i class="fa fa-ellipsis-h"></i>
     </span>
     <h4 class="text-section">Contents</h4>
    </li>
    <?php renderSidebarItems($contentSidebarItems, $currentPage, $currentUser); ?>

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