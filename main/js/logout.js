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
   // Clear local storage and session storage
   localStorage.clear();
   sessionStorage.clear();

   // Use AJAX to handle logout
   fetch('main/common/logout.php', {
    method: 'POST',
    credentials: 'same-origin'
   })
    .then(response => {
     if (response.ok) {
      Swal.fire({
       title: 'Logged Out',
       text: 'You have been successfully logged out.',
       icon: 'success',
       confirmButtonText: 'OK'
      }).then(() => {
       // Redirect to login page
       window.location.href = 'main/common/login.php';
      });
     } else {
      Swal.fire({
       title: 'Logout Error',
       text: 'An error occurred during logout.',
       icon: 'error'
      });
     }
    })
    .catch(error => {
     console.error('Logout error:', error);
     Swal.fire({
      title: 'Logout Error',
      text: 'An unexpected error occurred.',
      icon: 'error'
     });
    });
  }
 });
}