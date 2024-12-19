document.addEventListener('DOMContentLoaded', function () {
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

 // Add confirmation prompt for the Save button
 const saveButton = document.querySelector('form#editProfile button[type="submit"]');
 saveButton.addEventListener('click', function (event) {
  event.preventDefault(); // Prevent the form from submitting immediately

  Swal.fire({
   title: 'Are you sure?',
   text: 'Do you want to save the changes to your profile?',
   icon: 'question',
   showCancelButton: true,
   confirmButtonText: 'Yes, save it!',
   cancelButtonText: 'Cancel'
  }).then((result) => {
   if (result.isConfirmed) {
    // Submit the form if the user confirms
    document.getElementById('editProfile').submit();
   }
  });
 });
});