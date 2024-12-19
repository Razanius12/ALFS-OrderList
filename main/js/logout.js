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
     // Check if the response is OK
     if (!response.ok) {
      throw new Error('Logout failed');
     }
     // Redirect directly without checking JSON
     window.location.href = 'main/common/login.php';
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