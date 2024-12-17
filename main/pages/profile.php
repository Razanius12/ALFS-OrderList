<?php
require 'config/database.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['level'])) {
 header('Location: main/common/login.php');
 exit();
}

// Determine user type and retrieve data from the correct table
$userId = $_SESSION['user_id'];
$userLevel = $_SESSION['level'];
$userData = [];

if ($userLevel === 'admin') {
 $query = "SELECT * FROM admins WHERE id_admin = ?";
} else if ($userLevel === 'worker') {
 $query = "SELECT * FROM workers WHERE id_worker = ?";
} else {
 echo "Invalid user level.";
 exit();
}

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
 $userData = $result->fetch_assoc();
} else {
 echo "User not found.";
 exit();
}
$stmt->close();

// Handle profile update submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 $username = $_POST['username'];
 $password = !empty($_POST['password']) ? $_POST['password'] : $userData['password'];
 $name = $_POST['name_worker'];
 $gender = $userLevel === 'worker' ? $_POST['gender_worker'] : null;
 $phoneNumber = $_POST['phone_number'];

 if ($userLevel === 'admin') {
  $updateQuery = "UPDATE admins SET username = ?, password = ?, name_admin = ?, phone_number = ? WHERE id_admin = ?";
  $updateStmt = $conn->prepare($updateQuery);
  $updateStmt->bind_param('ssssi', $username, $password, $name, $phoneNumber, $userId);
 } else if ($userLevel === 'worker') {
  $updateQuery = "UPDATE workers SET username = ?, password = ?, name_worker = ?, gender_worker = ?, phone_number = ? WHERE id_worker = ?";
  $updateStmt = $conn->prepare($updateQuery);
  $updateStmt->bind_param('sssssi', $username, $password, $name, $gender, $phoneNumber, $userId);
 }

 if ($updateStmt->execute()) {
  // Update session data
  $_SESSION['username'] = $username;
  $_SESSION['name'] = $name;
  if ($userLevel === 'admin') {
   $_SESSION['name_admin'] = $name;
  } else if ($userLevel === 'worker') {
   $_SESSION['name_worker'] = $name;
  }

  // Redirect to the profile page with a success message
  echo "<script>
  document.addEventListener('DOMContentLoaded', function() {
      Swal.fire({
          title: 'Success!',
          text: 'Profile updated successfully.',
          icon: 'success',
          confirmButtonText: 'OK'
      }).then(() => {
          window.location.href = 'index.php?page=profile&success=1';
      });
  });
</script>";

 } else {
  echo "Error updating profile: " . $conn->error;
 }
 $updateStmt->close();
}

$conn->close();
?>

<!-- Page Content -->
<div class="container">
 <div class="page-inner">

  <div class="page-header mb-0">
   <h3 class="fw-bold mb-3">User Profile</h3>
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
     <a href="index.php?page=profile">Profile</a>
    </li>
   </ul>
  </div>

  <div class="row">
   <div class="col-md-12">
    <div class="card">
     <div class="card-header">
      <div class="d-flex align-items-center">
       <h4 class="card-title">Customize Your Profile</h4>
      </div>
     </div>
     <form id="editProfile" method="POST" action="">

      <!-- the id is based on id just like in the login.php which can determine whether it's admin or worker -->
      <input type="hidden" name="id" id="editId" value="<?= htmlspecialchars($userId) ?>">

      <div class="card-body">
       <div class="row mt-3 justify-content-center">
        <div class="col">
         <div class="form-group">
          <label class="d-block mb-2">Profile Picture</label>
          <div class="avatar avatar-xl mx-auto">
           <div
            class="avatar-img rounded-circle bg-primary text-white d-flex align-items-center justify-content-center">
            <?= strtoupper(substr($userData['name_worker'] ?? $userData['name_admin'] ?? 'U', 0, 1)) ?>
           </div>
          </div>
         </div>
        </div>
       </div>
       <div class="row mt-3">
        <div class="col">
         <div class="form-group">
          <label>Username</label>
          <input type="text" class="form-control" name="username" id="edit_username"
           value="<?= htmlspecialchars($userData['username']) ?>" required>
         </div>
        </div>
       </div>
       <div class="row mt-3">
        <div class="col">
         <div class="form-group">
          <label>Password (leave as it is to keep current)</label>
          <div class="input-group">
           <input type="password" class="form-control" name="password" id="edit_password"
            value="<?= htmlspecialchars($userData['password']) ?>">
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
          <input type="text" class="form-control" name="name_worker" id="edit_name_worker"
           value="<?= htmlspecialchars($userData['name_worker'] ?? $userData['name_admin']) ?>" required>
         </div>
        </div>
       </div>
       <?php if ($userLevel === 'worker'): ?>
        <div class="row mt-3">
         <div class="col">
          <div class="form-group">
           <label>Gender</label>
           <select class="form-control" name="gender_worker" id="edit_gender" required>
            <option value="MALE" <?= ($userData['gender_worker'] ?? '') === 'MALE' ? 'selected' : '' ?>>Male</option>
            <option value="FEMALE" <?= ($userData['gender_worker'] ?? '') === 'FEMALE' ? 'selected' : '' ?>>Female</option>
            <option value="OTHER" <?= ($userData['gender_worker'] ?? '') === 'OTHER' ? 'selected' : '' ?>>Other</option>
           </select>
          </div>
         </div>
        </div>
       <?php endif; ?>
       <div class="row mt-3">
        <div class="col">
         <div class="form-group">
          <label>Phone Number</label>
          <input type="tel" class="form-control" name="phone_number" id="edit_phone_number"
           value="<?= htmlspecialchars($userData['phone_number']) ?>" required>
         </div>
        </div>
       </div>
       <div class="text-end mt-3 mb-3">
        <button class="btn btn-success" type="submit">Save</button>
       </div>
      </div>
     </form>
    </div>
   </div>
  </div>
 </div>
</div>