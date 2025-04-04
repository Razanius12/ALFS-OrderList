document.addEventListener('DOMContentLoaded', function () {

 $(document).ready(function () {
  if ($.fn.DataTable) {
   $('#workers-table').DataTable({
    "pageLength": 10,
    "order": [[0, "desc"]],
    responsive: true,
    language: {
     search: "_INPUT_",
     searchPlaceholder: "Search workers...",
     lengthMenu: "Show _MENU_ entries"
    },
    columnDefs: [
     {
      targets: 0,
      visible: false
     }
    ],
    // Callback function to initialize password toggle after DataTable is created
    initComplete: function () {
     initializePasswordToggle();
    }
   });

   // Initialize password toggle functionality
   function initializePasswordToggle() {
    // Password toggle functionality
    $('#workers-table').on('click', '.toggle-password', function () {
     const passwordText = $(this).siblings('.password-text');
     const currentPassword = passwordText.attr('data-password');
     const isHidden = passwordText.text().includes('•');

     if (isHidden) {
      passwordText.text(currentPassword);
      $(this).find('i').removeClass('fa-eye').addClass('fa-eye-slash');
     } else {
      passwordText.text(maskPassword(currentPassword));
      $(this).find('i').removeClass('fa-eye-slash').addClass('fa-eye');
     }
    });
   }
  }

  // Function to explicitly mask a password field
  function maskPassword(password) {
   return password.replace(/./g, '•');
  }

  // Function to toggle password visibility
  function togglePassword(inputField, icon) {
   if (inputField.attr('type') === 'password') {
    inputField.attr('type', 'text');
    icon.removeClass('fa-eye').addClass('fa-eye-slash');
   } else {
    inputField.attr('type', 'password');
    icon.removeClass('fa-eye-slash').addClass('fa-eye');
   }
  }

  // Initialize password toggle for modals
  function initializePasswordToggleInModals() {
   $('.toggle-password').off('click').on('click', function () {
    const inputField = $(this).siblings('input');
    const icon = $(this).find('i');
    togglePassword(inputField, icon);
   });
  }

  // Add Worker Modal: Mask the password field when opened
  $('#addWorkerModal').on('show.bs.modal', function () {
   const passwordField = $('#addWorkerForm input[name="password"]');
   passwordField.val(''); // Clear the password field
   passwordField.attr('type', 'password'); // Ensure type is password
   initializePasswordToggleInModals();
  });

  // Edit Worker Modal Handler
  $('#editWorkerModal').on('show.bs.modal', function (e) {
   const button = $(e.relatedTarget);
   const workerId = button.data('worker-id');
   const passwordField = $('#addWorkerForm input[name="password"]');
   passwordField.val('');
   passwordField.attr('type', 'password');
   initializePasswordToggleInModals();

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
    dataType: 'json',
    success: function (response) {
     if (response.success) {
      const data = response.data;

      // Populate form fields
      $('#edit_worker_id').val(data.id_worker);
      $('#edit_name_worker').val(data.name_worker);
      $('#edit_username').val(data.username);
      $('#edit_position').val(data.id_position);
      $('#edit_gender').val(data.gender_worker);
      $('#edit_phone_number').val(data.phone_number);

      // Mask password
      const passwordField = $('#editWorkerForm input[name="password"]');
      passwordField.val(data.password || ''); // Populate if password exists
      passwordField.attr('type', 'password'); // Ensure type is password

      // Add order assignment population
      $('#edit_assigned_order').val(data.assigned_order_id || 'null');

      // Use order_count for further logic
      const orderCount = data.order_count;

      // Add functionality to the search button
      $('#searchOrdersButton').off('click').on('click', function () {
       const workerName = data.name_worker; // Get the worker's name

       // Check if order count is 0
       if (orderCount == 0) {
        // Show Swal message
        Swal.fire({
         icon: 'info',
         title: 'No Related Orders',
         text: 'This worker has no related orders.',
         confirmButtonText: 'OK'
        });
       } else {
        // Redirect to the orderData page with the worker's name as a query parameter
        window.location.href = 'index.php?page=orderData&name_worker=' + encodeURIComponent(workerName);
       }
      });

      initializePasswordToggleInModals();
      Swal.close();
     } else {
      Swal.fire({
       icon: 'error',
       title: 'Error',
       text: response.message || 'Failed to fetch worker data'
      });
     }
    },
    error: function (xhr, status, error) {
     Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Failed to fetch worker data: ' + error
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
      dataType: 'json',
      data: { id_worker: workerId },
      success: function (data) {
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

   // Create FormData object to handle form data
   var formData = new FormData(this);

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
    data: formData,
    processData: false,  // Important for FormData
    contentType: false,  // Important for FormData
    dataType: 'json',   // Explicitly tell jQuery to expect JSON
    success: function (data) {
     if (data.success) {
      Swal.fire({
       icon: 'success',
       title: 'Success',
       text: data.message || 'Worker updated successfully'
      }).then(() => {
       // Reload the page or update the table
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
    error: function (xhr, status, error) {
     console.error('Error details:', xhr.responseText);
     Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Failed to update worker: ' + (xhr.responseJSON?.message || error)
     });
    }
   });
  });

 });

});