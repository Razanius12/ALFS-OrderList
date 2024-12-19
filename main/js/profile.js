document.addEventListener('DOMContentLoaded', function () {
 // Helper function to mask password
 function maskPassword(password) {
  return password.replace(/./g, '•');
 }

 // Initialize password toggle for the profile form
 function initializePasswordToggle() {
  document.querySelectorAll('.toggle-password').forEach(toggle => {
   toggle.addEventListener('click', function () {
    const passwordField = this.previousElementSibling; // The password input field
    const icon = this.querySelector('i');

    if (passwordField.type === 'password') {
     passwordField.type = 'text';
     icon.classList.remove('fa-eye');
     icon.classList.add('fa-eye-slash');
    } else {
     passwordField.type = 'password';
     icon.classList.remove('fa-eye-slash');
     icon.classList.add('fa-eye');
    }
   });
  });
 }

 // Automatically mask password on page load
 const passwordField = document.getElementById('edit_password');
 if (passwordField && passwordField.value) {
  passwordField.type = 'password'; // Ensure the field is password type
 }

 // Initialize the password toggle
 initializePasswordToggle();
});