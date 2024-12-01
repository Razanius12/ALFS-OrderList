<?php
require 'session.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <link rel="icon" href="../img/ALFLogoLightSquareBlack.png" type="image/x-icon" />
 <title>Unauthorized Access</title>

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

 <style>
  body {
   margin: 0;
   padding: 0;
   display: flex;
   justify-content: center;
   align-items: center;
   height: 100vh;
   background-color: white;
   font-family: Arial, sans-serif;
  }
 </style>
</head>

<body>
 <!-- Sweet Alert -->
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

 <script>
  // Show SweetAlert on page load
  document.addEventListener('DOMContentLoaded', function () {
   Swal.fire({
    title: 'Unauthorized Access',
    text: 'You do not have permission to access this page.',
    icon: 'warning',
    confirmButtonText: 'Logout',
    allowOutsideClick: false,
    allowEscapeKey: false,
    showCancelButton: false,
    customClass: {
     confirmButton: 'btn btn-danger'
    }
   }).then((result) => {
    if (result.isConfirmed) {
     // AJAX call to logout
     fetch('logout.php', {
      method: 'POST'
     })
      .then(response => {
       // Redirect to login page
       window.location.href = 'login.php';
      })
      .catch(error => {
       console.error('Logout error:', error);
       // Fallback redirect
       window.location.href = 'login.php';
      });
    }
   });
  });
 </script>
</body>

</html>