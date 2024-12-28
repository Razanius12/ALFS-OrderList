<?php

workerOnlyPage();
$currentUser = getCurrentUserDetails();
checkPageAccess();

// Database connection
require 'config/database.php';

// Get current worker's ID from session
$currentWorkerId = $_SESSION['user_id'];

// Prepare the statement
$stmt = mysqli_prepare($conn, "SELECT 
           o.id_order, 
           o.order_name, 
           o.status, 
           o.start_date,
           o.finished_at,
           o.deadline,
           o.description,
           a.name_admin AS project_manager
          FROM 
           orders o
          LEFT JOIN 
           admins a ON o.project_manager_id = a.id_admin
          LEFT JOIN 
           workers w ON o.worker_id = w.id_worker
          WHERE 
          o.worker_id = ? AND o.status = 'COMPLETED'");

// Bind the parameter
mysqli_stmt_bind_param($stmt, "i", $currentWorkerId);

// Execute the statement
mysqli_stmt_execute($stmt);

// Get the result
$result = mysqli_stmt_get_result($stmt);

// Check if the query was successful
if ($result === false) {
 // Log the error
 error_log("Database Query Error: " . mysqli_error($conn));
 $error_message = "Unable to retrieve tasks. Please try again later.";
}

// Helper function to determine badge class based on status
function getStatusBadgeClass($status)
{
 switch (strtoupper($status)) {
  case 'COMPLETED':
   return 'success';
  case 'IN_PROGRESS':
   return 'info';
  case 'PENDING':
   return 'warning';
  case 'CANCELLED':
   return 'danger';
  default:
   return 'secondary';
 }
}

?>

<div class="container">
 <div class="page-inner">
  <div class="page-header mb-0">
   <h3 class="fw-bold mb-3">Task History</h3>
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
     <a href="index.php?page=history">Task History</a>
    </li>
   </ul>
  </div>

  <div class="row">
   <div class="col-md-12">
    <div class="card">
     <div class="card-header">
      <div class="d-flex align-items-center">
       <h4 class="card-title">Completed Tasks</h4>
      </div>
     </div>
     <div class="card-body">
      <div class="table-responsive">
       <table id="task-table" class="display table table-striped table-hover">
        <thead>
         <tr>
          <th data-orderable="true" style="display: none;">Task ID</th>
          <th>Task Name</th>
          <th>Task Duration</th>
          <th>Remaining Time</th>
          <th>Project Manager</th>
          <th>Task Status</th>
          <th style="width: 10%">Action</th>
         </tr>
        </thead>
        <tbody>
         <?php
         while ($task = mysqli_fetch_assoc($result)): ?>
          <tr>
           <td style="display: none;"><?= htmlspecialchars($task['id_order']) ?></td>
           <td><?= htmlspecialchars($task['order_name']) ?></td>
           <td>
            <?php
            // Calculate task duration
            if (!empty($task['start_date']) && !empty($task['finished_at'])) {
             $startDate = new DateTime($task['start_date']);
             $finishedDate = new DateTime($task['finished_at']);
             $duration = $startDate->diff($finishedDate);

             // Format duration
             if ($duration->days == 0) {
              $durationText = "Finished in less than a day";
             } elseif ($duration->days == 1) {
              $durationText = "Finished in 1 day";
             } else {
              $durationText = "Finished in " . $duration->days . " days";
             }

             echo '<span class="text-primary">' . htmlspecialchars($durationText) . '</span>';
            } else {
             echo '<span class="text-muted">N/A</span>';
            }
            ?>
           </td>
           <td>
            <?php
            // Calculate time relative to deadline
            if (!empty($task['finished_at']) && !empty($task['deadline'])) {
             $finishedDate = new DateTime($task['finished_at']);
             $deadlineDate = new DateTime($task['deadline']);

             // Compare finished date with deadline
             if ($finishedDate <= $deadlineDate) {
              $interval = $finishedDate->diff($deadlineDate);
              $daysBeforeDeadline = $interval->days;

              if ($daysBeforeDeadline == 0) {
               $timeText = '<span class="text-warning">Completed on deadline</span>';
              } elseif ($daysBeforeDeadline == 1) {
               $timeText = '<span class="text-success">Completed 1 day before deadline</span>';
              } else {
               $timeText = '<span class="text-success">Completed ' . $daysBeforeDeadline . ' days before deadline</span>';
              }
             } else {
              $interval = $deadlineDate->diff($finishedDate);
              $daysAfterDeadline = $interval->days;
              if ($daysAfterDeadline == 0) {
               $timeText = '<span class="text-warning">Completed on deadline</span>';
              } else {
               $timeText = '<span class="text-danger">Completed ' . $daysAfterDeadline . ' days after deadline</span>';
              }
             }

             echo $timeText;
            } else {
             echo '<span class="text-muted">N/A</span>';
            }
            ?>
           </td>
           <td><?= htmlspecialchars($task['project_manager'] ?? 'N/A') ?></td>
           <td>
            <span class="badge bg-<?= getStatusBadgeClass($task['status']) ?>">
             <?= htmlspecialchars($task['status']) ?>
            </span>
           </td>
           <td>
            <div class="form-button-action">
             <button type="button" class="btn btn-link btn-primary btn-lg" data-task-id="<?= $task['id_order'] ?>"
              data-task-name="<?= htmlspecialchars($task['order_name']) ?>"
              data-task-description="<?= htmlspecialchars($task['description'] ?? '') ?>"
              data-task-status="<?= htmlspecialchars($task['status']) ?>" onclick="showTaskDetails(this)">
              <i class="fa fa-eye"></i>
             </button>
             <?php if ($task['status'] === 'PENDING'): ?>
              <button type="button" class="btn btn-link btn-success" onclick="takeOrder(<?= $task['id_order'] ?>)">
               <i class="fa fa-play"></i>
              </button>
             <?php elseif ($task['status'] === 'IN_PROGRESS'): ?>
              <button type="button" class="btn btn-link btn-success" onclick="markTaskComplete(<?= $task['id_order'] ?>)">
               <i class="fa fa-check"></i>
              </button>
             <?php endif; ?>
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
</div>

<script src="main/js/histories.js"></script>