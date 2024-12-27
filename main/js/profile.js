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

 const deleteButton = document.getElementById('delete_profile_pic');

 let cropper;
 const profilePicInput = document.getElementById('profile_pic_input');
 const cropButton = document.getElementById('crop_button');
 const cancelButton = document.getElementById('cancel_button');
 const closeModalButton = document.getElementById('close_modal_button');
 const image = document.getElementById('image');

 profilePicInput.addEventListener('change', function (e) {
  const files = e.target.files;
  const done = (url) => {
   profilePicInput.value = '';
   image.src = url;
   $('#cropModal').modal({
    backdrop: 'static',
    keyboard: false
   }).modal('show');
  };
  let reader;
  let file;
  if (files && files.length > 0) {
   file = files[0];
   if (URL) {
    done(URL.createObjectURL(file));
   } else if (FileReader) {
    reader = new FileReader();
    reader.onload = function (e) {
     done(reader.result);
    };
    reader.readAsDataURL(file);
   }
  }
 });

 $(document).ready(function () {
  $('#cropModal').on('shown.bs.modal', function () {
   // Ensure the image element has the correct dimensions
   const image = document.getElementById('image');
   image.style.width = '100%';
   image.style.height = 'auto';

   cropper = new Cropper(image, {
    aspectRatio: 1,
    viewMode: 1,
   });
  }).on('hidden.bs.modal', function () {
   cropper.destroy();
   cropper = null;
  });
 });

 cropButton.addEventListener('click', function () {
  const canvas = cropper.getCroppedCanvas(); // Get the cropped canvas
  canvas.toBlob(function (blob) {
   const formData = new FormData();
   formData.append('profile_pic', blob, 'profile_pic.png');
   formData.append('action', 'upload');

   fetch('main/api/updateProfilePic.php', {
    method: 'POST',
    body: formData,
   })
    .then(response => response.json())
    .then(data => {
     if (data.success) {
      Swal.fire({
       title: 'Success!',
       text: data.message,
       icon: 'success',
       confirmButtonText: 'OK'
      }).then(() => {
       window.location.reload();
      });
     } else {
      Swal.fire({
       title: 'Error!',
       text: data.error,
       icon: 'error',
       confirmButtonText: 'OK'
      });
     }
    })
    .catch(error => {
     Swal.fire({
      title: 'Error!',
      text: 'Failed to upload file',
      icon: 'error',
      confirmButtonText: 'OK'
     });
    });

   $(document).ready(function () {
    $('#cropModal').modal('hide');
   });
  });
 });

 function showCloseWarning() {
  Swal.fire({
   title: 'Are you sure?',
   text: "You haven't cropped the image yet!",
   icon: 'warning',
   showCancelButton: true,
   confirmButtonText: 'Yes, close it!',
   cancelButtonText: 'No, keep it'
  }).then((result) => {
   if (result.isConfirmed) {
    $(document).ready(function () {
     $('#cropModal').modal('hide');
    });
   }
  });
 }

 cancelButton.addEventListener('click', showCloseWarning);
 closeModalButton.addEventListener('click', showCloseWarning);

 if (deleteButton) {
  deleteButton.addEventListener('click', async function (e) {
   e.preventDefault();

   const result = await Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, delete it!'
   });

   if (result.isConfirmed) {
    const formData = new FormData();
    formData.append('action', 'delete');

    try {
     const response = await fetch('main/api/updateProfilePic.php', {
      method: 'POST',
      body: formData
     });

     const data = await response.json();

     if (data.success) {
      Swal.fire({
       title: 'Deleted!',
       text: data.message,
       icon: 'success',
       confirmButtonText: 'OK'
      }).then(() => {
       window.location.reload();
      });
     } else {
      Swal.fire({
       title: 'Error!',
       text: data.error,
       icon: 'error',
       confirmButtonText: 'OK'
      });
     }
    } catch (error) {
     Swal.fire({
      title: 'Error!',
      text: 'An error occurred while deleting the profile picture',
      icon: 'error',
      confirmButtonText: 'OK'
     });
    }
   }
  });
 }

});