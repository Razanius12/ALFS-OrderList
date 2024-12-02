<?php
// Ensure session is started
if (session_status() == PHP_SESSION_NONE) {
 session_start();
}

// Capture reason
$reason = isset($_SESSION['unauthorized_reason'])
 ? $_SESSION['unauthorized_reason']
 : "Access Denied";
unset($_SESSION['unauthorized_reason']);

?>
<!DOCTYPE html>
<html lang="en">

<head>
 <meta charset="UTF-8">
 <title>Unauthorized Access</title>
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


 <!-- Sweet Alert -->
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
 <script>
  document.addEventListener('DOMContentLoaded', function () {
   Swal.fire({
    title: 'Unauthorized Access',
    text: '<?php echo addslashes($reason); ?>',
    icon: 'warning',
    confirmButtonText: 'Logout',
    allowOutsideClick: false,
    allowEscapeKey: false
   }).then(() => {
    // Redirect to login
    window.location.href = 'login.php';
   });
  });
 </script>
</body>

</html>