function confirmLogout() {
 Swal.fire({
  title: 'Log Out',
  text: 'Are you sure you want to log out?',
  icon: 'warning',
  showCancelButton: true,
  confirmButtonColor: '#3085d6',
  cancelButtonColor: '#d33',
  confirmButtonText: 'Yes, log out'
 }).then((result) => {
  if (result.isConfirmed) {
   // Comprehensive storage clearing
   localStorage.clear();
   sessionStorage.clear();

   // Clear all cookies
   document.cookie.split(";").forEach(function (c) {
    document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/");
   });

   // Use AJAX to handle logout
   fetch('main/common/logout.php', {
    method: 'POST',
    credentials: 'same-origin',
    headers: {
     'X-Requested-With': 'XMLHttpRequest'
    }
   })
    .then(response => {
     if (!response.ok) {
      throw new Error('Logout failed');
     }
     return response.json();
    })
    .then(data => {
     if (data.status === 'success') {
      Swal.fire({
       title: 'Logged Out',
       text: 'You have been successfully logged out.',
       icon: 'success',
       confirmButtonText: 'OK'
      }).then(() => {
       // Force full page reload and redirect
       window.location.href = data.redirect || 'main/common/login.php';
      });
     } else {
      throw new Error(data.message || 'Logout failed');
     }
    })
    .catch(error => {
     console.error('Logout error:', error);
     Swal.fire({
      title: 'Logout Error',
      text: error.message || 'An unexpected error occurred.',
      icon: 'error'
     });
    });
  }
 });
}