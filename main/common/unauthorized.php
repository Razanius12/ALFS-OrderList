<?php
// Ensure session is started
if (session_status() == PHP_SESSION_NONE) {
 session_start();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
 <meta charset="UTF-8">
 <title>Unauthorized Access</title>
 <link rel="icon" href="../img/ALFLogoLightSquareBlack.webp" type="image/x-icon" />

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
</head>

<body>
 <script>
  // Redirect to login
  window.location.href = 'login.php';
 </script>
</body>

</html>