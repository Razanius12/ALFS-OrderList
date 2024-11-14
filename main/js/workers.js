document.addEventListener('DOMContentLoaded', function () {
 $(document).ready(function () {
  // Password toggle function
  $(document).on('click', '.toggle-password', function (e) {
   e.preventDefault();

   const inputGroup = $(this).closest('.input-group');
   const passwordInput = inputGroup.find('input[type="password"], input[type="text"]');
   const icon = $(this).find('i');

   if (passwordInput.attr('type') === 'password') {
    passwordInput.attr('type', 'text');
    icon.removeClass('fa-eye').addClass('fa-eye-slash');
   } else {
    passwordInput.attr('type', 'password');
    icon.removeClass('fa-eye-slash').addClass('fa-eye');
   }
  });

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
      data: { id_worker: workerId },
      success: function (response) {
       const data = JSON.parse(response);
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
      error: function () {
       Swal.fire(
        'Error!',
        'Failed to delete worker',
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
    data: $(this).serialize(),
    success: function (response) {
     const data = JSON.parse(response);
     if (data.success) {
      Swal.fire({
       icon: 'success',
       title: 'Success',
       text: 'Worker added successfully'
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
    error: function () {
     Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Failed to add worker'
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