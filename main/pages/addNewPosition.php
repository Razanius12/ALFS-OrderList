<?php
// Database connection
require_once 'config/database.php';

// Handle form submission for adding a new position
if ($_SERVER["REQUEST_METHOD"] == "POST") {
 $position_name = trim($_POST['position_name']);
 $department = $_POST['department'];

 // Validate input
 $errors = [];

 if (empty($position_name)) {
  $errors[] = "Position name is required";
 } elseif (strlen($position_name) > 32) { // Match VARCHAR(32) constraint
  $errors[] = "Position name must be less than 32 characters";
 }

 if (empty($department)) {
  $errors[] = "Department is required";
 } elseif (!in_array($department, ['ADMIN', 'WORKER'])) { // Match ENUM constraint
  $errors[] = "Invalid department selected";
 }

 if (empty($errors)) {
  // Prepare SQL to insert new position
  $sql = "INSERT INTO positions (position_name, department) VALUES (?, ?)";

  try {
   $stmt = $conn->prepare($sql);
   $stmt->bind_param("ss", $position_name, $department);

   if ($stmt->execute()) {
    $success = "New position added successfully!";

    // Reset input fields after success
    $position_name = ''; // Clear position name
    $department = '';    // Clear department
   } else {
    $errors[] = "Error adding position: " . $stmt->error;
   }

   $stmt->close();
  } catch (Exception $e) {
   $errors[] = "Database error: " . $e->getMessage();
  }
 }
}

// Fetch existing positions with error handling
try {
 $positions_query = "SELECT id_position, position_name, department, created_at FROM positions ORDER BY created_at DESC";
 $result = $conn->query($positions_query);
 if (!$result) {
  throw new Exception($conn->error);
 }
} catch (Exception $e) {
 $fetch_error = "Error fetching positions: " . $e->getMessage();
}
?>

<div class="container">
 <div class="page-inner">
  <div class="page-header mb-0">
   <h3 class="fw-bold mb-3">Workers</h3>
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
     <a href="index.php?page=workers">Workers</a>
    </li>
    <li class="separator">
     <i class="icon-arrow-right"></i>
    </li>
    <li class="nav-item">
     <a href="index.php?page=addNewPosition">Add New Position</a>
    </li>
   </ul>
  </div>

  <div class="row">
   <div class="col-md-12">
    <div class="card">
     <div class="card-header">
      <h4 class="card-title">Add New Position</h4>
     </div>
     <div class="card-body">
      <!-- Display any errors -->
      <?php if (!empty($errors)): ?>
       <div class="alert alert-danger">
        <ul class="mb-0">
         <?php foreach ($errors as $error): ?>
          <li><?= htmlspecialchars($error) ?></li>
         <?php endforeach; ?>
        </ul>
       </div>
      <?php endif; ?>

      <!-- Display success message -->
      <?php if (isset($success)): ?>
       <div class="alert alert-success">
        <?= htmlspecialchars($success) ?>
       </div>
      <?php endif; ?>

      <!-- Add New Position Form -->
      <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>?page=addNewPosition">
       <div class="row">
        <div class="col-md-6">
         <div class="form-group">
          <label for="position_name">Position Name</label>
          <input type="text" class="form-control" id="position_name" name="position_name" maxlength="32" required
           value="<?= isset($success) ? '' : htmlspecialchars($_POST['position_name'] ?? '') ?>">
         </div>
        </div>
        <div class="col-md-6">
         <div class="form-group">
          <label for="department">Department</label>
          <select class="form-control" id="department" name="department" required>
           <option value="">Select Department</option>
           <option value="ADMIN" <?= (isset($success) ? '' : (isset($_POST['department']) && $_POST['department'] === 'ADMIN' ? 'selected' : '')) ?>>Admin</option>
           <option value="WORKER" <?= (isset($success) ? '' : (isset($_POST['department']) && $_POST['department'] === 'WORKER' ? 'selected' : '')) ?>>Worker</option>
          </select>
         </div>
        </div>
       </div>
       <div class="card-footer">
        <a href="index.php?page=workers" class="btn btn-secondary">Back</a>
        <button type="submit" class="btn btn-primary">Add Position</button>
       </div>
      </form>
     </div>
    </div>

    <!-- Positions Table -->
    <div class="card mt-4">
     <div class="card-header">
      <h4 class="card-title">Existing Positions</h4>
     </div>
     <div class="card-body">
      <?php if (isset($fetch_error)): ?>
       <div class="alert alert-danger">
        <?= htmlspecialchars($fetch_error) ?>
       </div>
      <?php else: ?>
       <div class="table-responsive">
        <table id="multi-filter-select" class="display table table-striped table-hover">
         <thead>
          <tr>
           <th>Position Name</th>
           <th>Department</th>
           <th>Created At</th>
           <th>Actions</th>
          </tr>
         </thead>
         <tbody>
          <?php while ($position = $result->fetch_assoc()): ?>
           <tr>
            <td><?= htmlspecialchars($position['position_name']) ?></td>
            <td><?= htmlspecialchars($position['department']) ?></td>
            <td><?= htmlspecialchars($position['created_at']) ?></td>
            <td>
             <div class="form-button-action">
              <button type="button" class="btn btn-link btn-primary btn-lg" data-toggle="modal"
               data-target="#editPositionModal" data-position-id="<?php echo $position['id_position']; ?>"
               data-position-name="<?php echo htmlspecialchars($position['position_name']); ?>"
               data-department="<?php echo htmlspecialchars($position['department']); ?>">
               <i class="fa fa-edit"></i>
              </button>
              <button type="button" class="btn btn-link btn-danger"
               onclick="deletePosition(<?= $position['id_position'] ?>)">
               <i class="fa fa-times"></i>
              </button>
             </div>
            </td>
           </tr>
          <?php endwhile; ?>
         </tbody>
        </table>
       </div>
      <?php endif; ?>
     </div>
    </div>
   </div>
  </div>
 </div>
</div>

<!-- Edit Position Modal -->
<div class="modal fade" id="editPositionModal" tabindex="-1" role="dialog" aria-labelledby="editPositionModalLabel"
 aria-hidden="true">
 <div class="modal-dialog" role="document">
  <div class="modal-content">
   <div class="modal-header">
    <h5 class="modal-title" id="editPositionModalLabel">Edit Position</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
     <span aria-hidden="true">&times;</span>
    </button>
   </div>
   <form id="editPositionForm">
    <div class="modal-body">
     <div id="editPositionErrorContainer" class="alert alert-danger" style="display:none;"></div>

     <input type="hidden" name="id_position" id="editPositionId">

     <div class="form-group">
      <label for="editPositionName">Position Name</label>
      <input type="text" class="form-control" id="editPositionName" name="position_name" maxlength="32" required>
     </div>

     <div class="form-group">
      <label for="editPositionDepartment">Department</label>
      <select class="form-control" id="editPositionDepartment" name="department" required>
       <option value="ADMIN">Admin</option>
       <option value="WORKER">Worker</option>
      </select>
     </div>
    </div>
    <div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
     <button type="submit" class="btn btn-primary">Save Changes</button>
    </div>
   </form>
  </div>
 </div>
</div>


<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
 // Function to populate edit modal
 document.addEventListener('DOMContentLoaded', function () {
  // Ensure modal trigger buttons are correctly set up
  const editButtons = document.querySelectorAll('.edit-position-btn');

  editButtons.forEach(button => {
   button.addEventListener('click', function () {
    // Get position details from data attributes
    const positionId = this.getAttribute('data-position-id');
    const positionName = this.getAttribute('data-position-name');
    const department = this.getAttribute('data-department');

    // Populate modal fields
    document.getElementById('editPositionId').value = positionId;
    document.getElementById('editPositionName').value = positionName;
    document.getElementById('editPositionDepartment').value = department;

    // Show the modal
    $('#editPositionModal').modal('show');
   });
  });

  // Handle form submission
  document.getElementById('editPositionForm').addEventListener('submit', function (e) {
   e.preventDefault();

   const formData = new FormData(this);

   fetch('main/api/updatePosition.php', {
    method: 'POST',
    body: formData
   })
    .then(response => response.json())
    .then(data => {
     if (data.success) {
      // Update table row
      const positionId = formData.get('id_position');
      const row = document.querySelector(`.edit-position-btn[data-position-id="${positionId}"]`).closest('tr');

      row.cells[1].textContent = formData.get('position_name');
      row.cells[2].textContent = formData.get('department');

      // Close modal
      $('#editPositionModal').modal('hide');

      // Show success message
      alert(data.message || 'Position updated successfully');
     } else {
      // Show error in modal
      const errorContainer = document.getElementById('editPositionErrorContainer');
      errorContainer.innerHTML = data.message || 'Error updating position';
      errorContainer.style.display = 'block';
     }
    })
    .catch(error => {
     console.error('Error:', error);
     const errorContainer = document.getElementById('editPositionErrorContainer');
     errorContainer.innerHTML = 'An unexpected error occurred';
     errorContainer.style.display = 'block';
    });
  });
 });

 function deletePosition(positionId) {
  if (confirm('Are you sure you want to delete this position?')) {
   const deleteUrl = 'main/api/deletePosition.php?id=' + encodeURIComponent(positionId);

   fetch(deleteUrl, {
    method: 'GET',
    headers: {
     'Accept': 'application/json',
     'Content-Type': 'application/json'
    }
   })
    .then(response => {
     if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
     }
     return response.json();
    })
    .then(data => {
     if (data.success) {
      // Try multiple ways to find and remove the row
      const row = document.querySelector(`tr button[onclick="deletePosition(${positionId})"]`)?.closest('tr');

      if (row) {
       row.remove();
      } else {
       console.warn(`Could not find row for position ${positionId}`);
      }

      alert(data.message || 'Position deleted successfully');
     } else {
      alert(data.message || 'Error deleting position');
     }
    })
    .catch(error => {
     console.error('Delete Error:', error);
     alert('Error deleting position: ' + error.message);
    });
  }
 }

 // Optional: Add edit functionality
 function editPosition(positionId) {
  // Implement edit modal or inline editing logic
  console.log('Edit position:', positionId);
 }
</script>