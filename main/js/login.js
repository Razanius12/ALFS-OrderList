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

  // Login form submission
  $('#loginForm').on('submit', function (e) {
   e.preventDefault();

   // Disable submit button and show loading
   const submitBtn = $(this).find('button[type="submit"]');
   submitBtn.prop('disabled', true);

   Swal.fire({
    title: 'Logging In',
    text: 'Please wait while we authenticate your credentials...',
    icon: 'info',
    allowOutsideClick: false,
    showConfirmButton: false,
    didOpen: () => {
     Swal.showLoading();
    }
   });

   $.ajax({
    url: $(this).attr('action'),
    method: 'POST',
    dataType: 'json',
    data: $(this).serialize(),
    success: function (response) {
     // Success notification
     Swal.fire({
      icon: 'success',
      title: 'Login Successful!',
      text: 'Redirecting to dashboard...',
      timer: 2000,
      showConfirmButton: true,
      willClose: () => {
       // Use the redirect URL from the response
       window.location.href = response.redirect;
      }
     });
    },
    error: function (xhr) {
     // Get error message from response
     const errorMessage = xhr.responseJSON
      ? xhr.responseJSON.message
      : 'An unexpected error occurred';

     // Error notification
     Swal.fire({
      icon: 'error',
      title: 'Login Failed',
      text: errorMessage,
      confirmButtonText: 'Try Again',
      confirmButtonColor: '#3085d6'
     });
    },
    complete: function () {
     // Re-enable submit button
     submitBtn.prop('disabled', false).html('Login');
    }
   });
  });
 });
});