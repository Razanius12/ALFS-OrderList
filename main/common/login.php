<?php
require_once '../../config/database.php';

// Handle login error messages
$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);

// Additional error handling
$login_attempt_error = $_SESSION['login_attempt_error'] ?? '';
unset($_SESSION['login_attempt_error']);
?>

<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
 <meta http-equiv="X-UA-Compatible" content="IE=edge" />
 <title>ALF Solution Order List</title>
 <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
 <link rel="icon" href="../img/ALFLogoLightSquareBlack.png" type="image/x-icon" />

 <!-- Fonts and icons -->
 <script src="../../assets/js/plugin/webfont/webfont.min.js"></script>
 <script>
  WebFont.load({
   google: { families: ["Public Sans:300,400,500,600,700"] },
   custom: {
    families: [
     "Font Awesome 5 Solid",
     "Font Awesome 5 Regular",
     "Font Awesome 5 Brands",
     "simple-line-icons",
    ],
    urls: ["../../assets/css/fonts.min.css"],
   },
   active: function () {
    sessionStorage.fonts = true;
   },
  });
 </script>

 <!-- CSS Files -->
 <link rel="stylesheet" href="../../assets/css/bootstrap.min.css" />
 <link rel="stylesheet" href="../../assets/css/plugins.min.css" />
 <link rel="stylesheet" href="../../assets/css/kaiadmin.min.css" />
 <link rel="stylesheet" href="../css/login.css" />
</head>

<body>
 <div class="login-container">
  <div class="login-card">
   <div class="login-header">
    <h2>ALFS OrderList Login</h2>
    <?php if ($error): ?>
     <div class="error-message">
      <?php echo htmlspecialchars($error); ?>
     </div>
    <?php endif; ?>
   </div>

   <form id="loginForm" method="POST" action="../api/loginProcess.php">
    <div class="mb-3">
     <label for="username" class="form-label">Username</label>
     <input type="text" class="form-control" id="username" name="username" required autofocus
      placeholder="Enter your username">
    </div>

    <div class="mb-3">
     <label for="password" class="form-label">Password</label>
     <div class="input-group">
      <input type="password" class="form-control" id="password" name="password" required
       placeholder="Enter your password">
      <button class="btn toggle-password" type="button" title="Show/Hide Password">
       <i class="fa fa-eye"></i>
      </button>
     </div>
    </div>

    <div class="mb-3 form-check">
     <input type="checkbox" class="form-check-input" id="rememberMe" name="remember_me">
     <label class="form-check-label" for="rememberMe">Remember me</label>
    </div>

    <button type="submit" class="btn btn-primary btn-login">
     Login
    </button>
   </form>

   <div class="text-center mt-3">
    <a href="javascript:void(0);" onclick="fetchAdminContacts()" class="text-muted">Forgot Password?</a>
   </div>
  </div>
 </div>

 <div class="preload-background"></div>
 <div class="loading-overlay">
  <div class="loading-spinner"></div>
 </div>

</body>
<!--   Core JS Files   -->
<script src="../../assets/js/core/jquery-3.7.1.min.js"></script>
<script src="../../assets/js/core/popper.min.js"></script>
<script src="../../assets/js/core/bootstrap.min.js"></script>

<!-- Kaiadmin JS -->
<script src="../../assets/js/kaiadmin.min.js"></script>
<!-- Sweet Alert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
 function fetchAdminContacts() {
  fetch('../api/getAdminContacts.php')
   .then(response => {
    if (!response.ok) {
     throw new Error('Network response was not ok');
    }
    return response.json();
   })
   .then(response => {
    if (response.success && response.data && response.data.length > 0) {
     // Construct HTML for admin contacts
     const contactsHTML = response.data.map(admin =>
      `<div class="d-flex justify-content-between mb-2">
         <strong>${admin.name_admin}</strong>
         <span class="badge bg-primary">${admin.phone_number}</span>
        </div>`
     ).join('');

     Swal.fire({
      title: 'Forgot Password?',
      html: `
             <p>Please contact one of the following administrators to reset your password:</p>
              ${contactsHTML}
              <hr class="my-3">
              <small class="text-muted">
                <strong>Important Notice:</strong> 
                Password reset assistance is strictly limited to authorized personnel. 
                Unauthorized individuals attempting to contact administrators may face 
                legal or administrative consequences.
              </small>
            `,
      icon: 'info',
      confirmButtonText: 'Close',
      customClass: {
       popup: 'swal-wide'
      }
     });
    } else {
     Swal.fire({
      title: 'No Contacts Available',
      text: response.message || 'Unable to retrieve administrator contacts at this time.',
      icon: 'warning',
      confirmButtonText: 'OK'
     });
    }
   })
   .catch(error => {
    console.error('Error fetching admin contacts:', error);
    Swal.fire({
     title: 'Error',
     text: 'Unable to fetch administrator contacts. Please check your connection and try again.',
     icon: 'error',
     confirmButtonText: 'OK'
    });
   });
 }
 document.addEventListener('DOMContentLoaded', function () {
  // Hide loading overlay when page is fully loaded
  const loadingOverlay = document.querySelector('.loading-overlay');
  const backgroundImage = new Image();

  backgroundImage.onload = function () {
   loadingOverlay.style.opacity = '0';
   setTimeout(() => {
    loadingOverlay.style.display = 'none';
   }, 500);
  };

  backgroundImage.onerror = function () {
   // Fallback to white background if image fails to load
   document.body.style.backgroundImage = 'none';
   loadingOverlay.style.opacity = '0';
   setTimeout(() => {
    loadingOverlay.style.display = 'none';
   }, 500);
  };

  // Preload the background image
  backgroundImage.src = '../img/DSCF7610.JPG';

  // Prevent default scroll behavior
  document.body.style.overflow = 'hidden';
  document.documentElement.style.overflow = 'hidden';

  // Optional: Adjust for mobile devices
  document.body.addEventListener('touchmove', function (e) {
   e.preventDefault();
  }, { passive: false });

  $(document).ready(function () {
   // Password toggle script
   const passwordToggle = document.querySelector('.toggle-password');
   const passwordInput = document.getElementById('password');

   passwordToggle.addEventListener('click', function () {
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);

    // Toggle eye icon
    const eyeIcon = this.querySelector('i');
    eyeIcon.classList.toggle('fa-eye');
    eyeIcon.classList.toggle('fa-eye-slash');
   });

   // Initial error handling from PHP
   <?php if (!empty($error)): ?>
    Swal.fire({
     title: 'Login Error',
     text: '<?php echo addslashes($error); ?>',
     icon: 'error',
     confirmButtonText: 'Try Again'
    });
   <?php endif; ?>

   <?php if (!empty($login_attempt_error)): ?>
    Swal.fire({
     title: 'Login Attempt Failed',
     text: '<?php echo addslashes($login_attempt_error); ?>',
     icon: 'warning',
     confirmButtonText: 'OK'
    });
   <?php endif; ?>

   // Enhanced form submission handling
   $('#loginForm').on('submit', function (e) {
    e.preventDefault();

    const username = $('#username').val().trim();
    const password = $('#password').val().trim();

    if (!username || !password) {
     Swal.fire({
      title: 'Invalid Input',
      text: 'Please enter your username and password',
      icon: 'warning',
      confirmButtonText: 'OK'
     });
     return;
    }

    $.ajax({
     url: '../api/loginProcess.php',
     method: 'POST',
     data: $(this).serialize(),
     dataType: 'json',
     success: function (response) {
      if (response.status === 'success') {
       // Log session data for debugging
       console.log('Session Data:', response.session_data);

       Swal.fire({
        title: 'Login Successful',
        text: 'Redirecting to dashboard',
        icon: 'success',
        showConfirmButton: true
       }).then((result) => {
        if (result.isConfirmed) {
         window.location.href = response.redirect;
        }
       });
      } else {
       Swal.fire({
        title: 'Login Failed',
        text: response.message || 'An unexpected error occurred',
        icon: 'error',
        confirmButtonText: 'Try Again'
       });
      }
     },
     error: function (xhr, status, error) {
      console.error('AJAX Error:', status, error);
      Swal.fire({
       title: 'Error',
       text: 'An unexpected error occurred',
       icon: 'error',
       confirmButtonText: 'OK'
      });
     }
    });
   });

  });

  // Forgot Password Event Listener
  const forgotPasswordLink = document.getElementById('forgotPasswordLink');
  if (forgotPasswordLink) {
   forgotPasswordLink.addEventListener('click', function (e) {
    e.preventDefault();
    fetchAdminContacts();
   });
  }

 });
</script>

</html>