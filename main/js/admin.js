document.addEventListener('DOMContentLoaded', function () {

 $(document).ready(function () {
  if ($.fn.DataTable) {
   $('#admin-table').DataTable({
    "pageLength": 10,
    "order": [[0, "desc"]],
    responsive: true,
    language: {
     search: "_INPUT_",
     searchPlaceholder: "Search admins...",
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
    $('#admin-table').on('click', '.toggle-password', function () {
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

  // Helper function to mask password
  function maskPassword(password) {
   return password.replace(/./g, '•');
  }

  // Edit Admin Modal Handler
  $('#editAdminModal').on('show.bs.modal', function (e) {
   const button = $(e.relatedTarget);
   const adminId = button.data('admin-id');

   // Show loading state
   Swal.fire({
    title: 'Loading...',
    allowOutsideClick: false,
    didOpen: () => {
     Swal.showLoading();
    }
   });

   // Fetch admin data
   $.ajax({
    url: 'main/api/getAdmin.php',
    type: 'GET',
    data: { id_admin: adminId },
    dataType: 'json',
    success: function (response) {
     if (response.success) {
      const data = response.data;

      // Populate form fields
      $('#edit_admin_id').val(data.id_admin);
      $('#edit_name_admin').val(data.name_admin);
      $('#edit_username').val(data.username);
      $('#edit_position').val(data.id_position);
      $('#edit_phone_number').val(data.phone_number);

      // Populate password field if applicable
      // SECURITY WARNING: Be very careful about handling passwords
      $('#edit_password').val(data.password);

      Swal.close();
     } else {
      Swal.fire({
       icon: 'error',
       title: 'Error',
       text: response.message || 'Failed to fetch admin data'
      });
     }
    },
    error: function (xhr, status, error) {
     Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Failed to fetch admin data: ' + error
     });
    }
   });
  });

  // Delete Admin Handler
  window.deleteAdmin = function (adminId) {
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
      url: 'main/api/deleteAdmin.php',
      type: 'POST',
      dataType: 'json',
      data: { id_admin: adminId },
      success: function (data) {
       if (data.success) {
        Swal.fire(
         'Deleted!',
         'Admin has been deleted.',
         'success'
        ).then(() => {
         location.reload();
        });
       } else {
        // Check if the error is due to existing project assignments
        if (data.message === 'Cannot delete admin with existing project assignments.') {
         Swal.fire(
          'Error!',
          data.message,
          'error'
         );
        } else {
         Swal.fire(
          'Error!',
          data.message || 'Failed to delete admin',
          'error'
         );
        }
       }
      },
      error: function (xhr, status, error) {
       console.error('Error details:', xhr.responseText, status, error);
       Swal.fire(
        'Error!',
        'Failed to delete admin: ' + xhr.responseText,
        'error'
       );
      }
     });
    }
   });
  };

  // Add Admin Form Submit Handler
  $('#addAdminForm').on('submit', function (e) {
   e.preventDefault();

   Swal.fire({
    title: 'Adding Admin...',
    allowOutsideClick: false,
    didOpen: () => {
     Swal.showLoading();
    }
   });

   $.ajax({
    url: $(this).attr('action'),
    type: 'POST',
    dataType: 'json',
    data: $(this).serialize(),
    success: function (data) {
     if (data.success) {
      Swal.fire({
       icon: 'success',
       title: 'Success',
       text: data.message || 'Admin added successfully'
      }).then(() => {
       location.reload();
      });
     } else {
      Swal.fire({
       icon: 'error',
       title: 'Error',
       text: data.message || 'Failed to add admin'
      });
     }
    },
    error: function (xhr, status, error) {
     console.error('AJAX Error:', status, error);
     Swal.fire({
      icon: 'error',
      title: 'Error',
      text: xhr.responseText || 'Failed to add admin'
     });
    }
   });
  });

  // Edit Admin Form Submit Handler
  $('#editAdminForm').on('submit', function (e) {
   e.preventDefault();

   // Create FormData object to handle file uploads and more complex form data
   var formData = new FormData(this);

   Swal.fire({
    title: 'Updating Admin...',
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
       text: data.message || 'Admin updated successfully'
      }).then(() => {
       location.reload();
      });
     } else {
      Swal.fire({
       icon: 'error',
       title: 'Error',
       text: data.message || 'Failed to update admin'
      });
     }
    },
    error: function (xhr, status, error) {
     console.error('Error details:', xhr.responseText);
     Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Failed to update admin: ' + (xhr.responseJSON?.message || error)
     });
    }
   });
  });

 });
});