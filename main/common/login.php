<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/session.php';

// Check if already logged in
if ($auth->isLoggedIn()) {
 header("Location: '../../index.php'");
 exit();
}

// Handle login error messages
$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>

<!DOCTYPE html>
<html lang="en" class="h-100">

<head>

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
 </head>
 <style>
  /* Full-page background with placeholder */
  html,
  body {
   height: 100% !important;
   margin: 0 !important;
   background-color: #ffffff !important;
   background-image: url('../img/DSCF7610.JPG') !important;

   /* Shift to right and zoom */
   background-position: 0% center !important;
   background-size: 120% auto !important;
   /* Slight zoom */

   background-repeat: no-repeat !important;
   background-attachment: fixed !important;

   /* Smooth transition for zoom and position */
   transition:
    background-position 0.5s ease-in-out,
    background-size 0.5s ease-in-out !important;
  }

  /* Optional: Responsive adjustments */
  @media (max-width: 768px) {

   html,
   body {
    background-position: center center !important;
    background-size: cover !important;
   }
  }

  /* Preload and placeholder styles */
  body::before {
   content: "";
   position: fixed;
   top: 0;
   left: 0;
   width: 100%;
   height: 100%;
   background-color: #ffffff;
   /* White background placeholder */
   z-index: -1;
   opacity: 1;
   transition: opacity 0.5s ease-in-out;
  }

  /* Image preload technique */
  .preload-background {
   background-image: url('../img/DSCF7610.JPG');
   position: absolute;
   width: 1px;
   height: 1px;
   opacity: 0;
   z-index: -999;
  }

  /* Loading indicator (optional) */
  .loading-overlay {
   position: fixed;
   top: 0;
   left: 0;
   width: 100%;
   height: 100%;
   background-color: #ffffff;
   display: flex;
   justify-content: center;
   align-items: center;
   z-index: 9999;
   opacity: 1;
   transition: opacity 0.5s ease-in-out;
  }

  .loading-spinner {
   width: 50px;
   height: 50px;
   border: 5px solid #f3f3f3;
   border-top: 5px solid #3498db;
   border-radius: 50%;
   animation: spin 1s linear infinite;
  }

  @keyframes spin {
   0% {
    transform: rotate(0deg);
   }

   100% {
    transform: rotate(360deg);
   }
  }

  .login-container {
   display: flex !important;
   justify-content: center !important;
   align-items: center !important;
   height: 100vh !important;
  }

  .login-card {
   width: 100% !important;
   max-width: 450px !important;
   padding: 20px !important;
   box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.4) !important;
   border-radius: 8px !important;
   background: white !important;
  }

  .login-header {
   text-align: center !important;
   margin-bottom: 20px !important;
  }

  .login-header h2 {
   color: #333 !important;
   font-weight: 600 !important;
  }

  .form-control:focus {
   border-color: #007bff !important;
   box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25) !important;
  }

  .btn-login {
   width: 100% !important;
   padding: 10px !important;
   font-weight: 600 !important;
  }

  .error-message {
   color: #dc3545 !important;
   margin-bottom: 15px !important;
   text-align: center !important;
  }

  /* Password toggle */
  .input-group-text {
   cursor: pointer !important;
   transition: background-color 0.3s ease;
  }

  .toggle-password {
   background: transparent !important;
   border: none !important;
   color: #6c757d !important;
   transition: color 0.3s ease !important;
  }

  .toggle-password:hover {
   color: #007bff !important;
   background: rgba(0, 123, 255, 0.1) !important;
   outline: none !important;
  }

  .toggle-password:focus {
   outline: none !important;
   box-shadow: none !important;
  }
 </style>
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
    <a href="#" class="text-muted">Forgot Password?</a>
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

<script src="../js/login.js"></script>

</html>