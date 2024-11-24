document.addEventListener('DOMContentLoaded', function () {
 $(document).ready(function () {
  // Toggle password visibility for both static table and modal forms
  $(document).on('click', '.toggle-password', function () {
   const $container = $(this).closest('.password-container, .input-group');
   const $passwordInput = $container.find('input, .password-text');
   const $eyeIcon = $(this).find('i');

   // Check if it's an input or a span
   if ($passwordInput.is('input')) {
    // For input fields
    if ($passwordInput.attr('type') === 'password') {
     $passwordInput.attr('type', 'text');
     $eyeIcon.removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
     $passwordInput.attr('type', 'password');
     $eyeIcon.removeClass('fa-eye-slash').addClass('fa-eye');
    }
   } else if ($passwordInput.hasClass('password-text')) {
    // For span elements with masked passwords
    const currentText = $passwordInput.text();
    const actualPassword = $passwordInput.attr('data-password');

    if (currentText === actualPassword) {
     // Currently showing password, hide it
     $passwordInput.text(maskPassword(actualPassword));
     $eyeIcon.removeClass('fa-eye-slash').addClass('fa-eye');
    } else {
     // Currently masked, show password
     $passwordInput.text(actualPassword);
     $eyeIcon.removeClass('fa-eye').addClass('fa-eye-slash');
    }
   }
  });

  // Helper function to mask password
  function maskPassword(password) {
   return password.replace(/./g, '•');
  }

  // Edit Worker Modal Handler
  $('#editWorkerModal').on('show.bs.modal', function (e) {
   const button = $(e.relatedTarget);
   const workerId = button.data('worker-id');

   // Show loading state
   Swal.fire({
    title: 'Loading...',
    allowOutsideClick: false,
    didOpen: () => {
     Swal.showLoading();
    }
   });

   // Fetch worker data
   $.ajax({
    url: 'main/api/getWorker.php',
    type: 'GET',
    data: { id_worker: workerId },
    success: function (response) {
     const data = JSON.parse(response);

     // Populate form fields
     $('#edit_worker_id').val(data.id_worker);
     $('#edit_name_worker').val(data.name_worker);
     $('#edit_username').val(data.username);
     $('#edit_position').val(data.id_position);
     $('#edit_gender').val(data.gender_worker);
     $('#edit_phone_number').val(data.phone_number);

     Swal.close();
    },
    error: function () {
     Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Failed to fetch worker data'
     });
    }
   });
  });

  // Delete Worker Handler
  window.deleteWorker = function (workerId) {
   Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, delete it!'
   }).then((result) => {
    if (result.isConfirmed) {
     $.ajax({
      url: 'main/api/deleteWorker.php',
      type: 'POST',
      dataType: 'json', // This will automatically parse JSON
      data: { id_worker: workerId },
      success: function (data) { // Changed from response to data
       if (data.success) {
        Swal.fire(
         'Deleted!',
         'Worker has been deleted.',
         'success'
        ).then(() => {
         location.reload();
        });
       } else {
        Swal.fire(
         'Error!',
         data.message || 'Failed to delete worker',
         'error'
        );
       }
      },
      error: function (xhr, status, error) {
       console.error('Error details:', xhr.responseText, status, error);
       Swal.fire(
        'Error!',
        'Failed to delete worker: ' + xhr.responseText,
        'error'
       );
      }
     });
    }
   });
  };

  // Add Worker Form Submit Handler
  $('#addWorkerForm').on('submit', function (e) {
   e.preventDefault();

   Swal.fire({
    title: 'Adding Worker...',
    allowOutsideClick: false,
    didOpen: () => {
     Swal.showLoading();
    }
   });

   $.ajax({
    url: $(this).attr('action'),
    type: 'POST',
    dataType: 'json', // Add this line to parse JSON automatically
    data: $(this).serialize(),
    success: function (data) { // Change response to data
     if (data.success) {
      Swal.fire({
       icon: 'success',
       title: 'Success',
       text: data.message || 'Worker added successfully'
      }).then(() => {
       location.reload();
      });
     } else {
      Swal.fire({
       icon: 'error',
       title: 'Error',
       text: data.message || 'Failed to add worker'
      });
     }
    },
    error: function (xhr, status, error) {
     // More detailed error handling
     console.error('AJAX Error:', status, error);
     console.log('Response Text:', xhr.responseText);

     Swal.fire({
      icon: 'error',
      title: 'Error',
      text: xhr.responseText || 'Failed to add worker'
     });
    }
   });
  });

  // Edit Worker Form Submit Handler
  $('#editWorkerForm').on('submit', function (e) {
   e.preventDefault();

   Swal.fire({
    title: 'Updating Worker...',
    allowOutsideClick: false,
    didOpen: () => {
     Swal.showLoading();
    }
   });

   $.ajax({
    url: $(this).attr('action'),
    type: 'POST',
    data: $(this).serialize(),
    success: function (response) {
     const data = JSON.parse(response);
     if (data.success) {
      Swal.fire({
       icon: 'success',
       title: 'Success',
       text: 'Worker updated successfully'
      }).then(() => {
       location.reload();
      });
     } else {
      Swal.fire({
       icon: 'error',
       title: 'Error',
       text: data.message || 'Failed to update worker'
      });
     }
    },
    error: function () {
     Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Failed to update worker'
     });
    }
   });
  });
 });

});