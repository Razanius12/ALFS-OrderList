document.addEventListener('DOMContentLoaded', function () {
 if ($.fn.DataTable) {
  $('#table-positions').DataTable({
   "pageLength": 10,
   "order": [[2, "desc"]],
   responsive: true,
   language: {
    search: "_INPUT_",
    searchPlaceholder: "Search positions...",
    lengthMenu: "Show _MENU_ entries"
   }
  });
 }

 const form = document.querySelector('form[method="POST"]');

 $(form).on('submit', function (event) {
  event.preventDefault();

  // Show loading state
  Swal.fire({
   title: 'Adding New Position...',
   allowOutsideClick: false,
   didOpen: () => {
    Swal.showLoading();
   }
  });

  const formData = new FormData(this);

  $.ajax({
   url: 'main/api/addPosition.php',
   type: 'POST',
   data: formData,
   processData: false,
   contentType: false,
   dataType: 'json',
   success: function (result) {
    if (result.success) {
     // Success Sweet Alert
     Swal.fire({
      icon: 'success',
      title: 'Success!',
      text: result.message,
      timerProgressBar: true,
      didClose: () => {
       window.location.reload(); // Reload page after alert closes
      }
     });
    } else {
     // Error Sweet Alert
     Swal.fire({
      icon: 'error',
      title: 'Oops...',
      html: Array.isArray(result.message)
       ? result.message.map(msg => `<div>${msg}</div>`).join('')
       : result.message,
      showConfirmButton: true
     });
    }
   },
   error: function (xhr, status, error) {
    // Network or unexpected error
    Swal.fire({
     icon: 'error',
     title: 'Error',
     text: 'An unexpected error occurred',
     footer: xhr.responseText || error.toString()
    });
   }
  });
 });

 // Edit Position Modal Handler
 $('#table-positions').on('click', '.btn-primary', function () {
  const positionId = $(this).data('position-id');
  const positionName = $(this).data('position-name');
  const department = $(this).data('department');

  // Show loading state
  Swal.fire({
   title: 'Loading...',
   allowOutsideClick: false,
   didOpen: () => {
    Swal.showLoading();
   }
  });

  // Fetch position data
  $.ajax({
   url: 'main/api/getPosition.php',
   type: 'GET',
   data: { id_position: positionId },
   dataType: 'json',
   success: function (response) {
    if (response.success) {
     const data = response.data;

     // Populate form fields
     $('#editPositionId').val(data.id_position);
     $('#editPositionName').val(data.position_name);
     $('#editPositionDepartment').val(data.department);

     // Show the modal
     $('#editPositionModal').modal('show');
     Swal.close();
    } else {
     Swal.fire({
      icon: 'error',
      title: 'Error',
      text: response.message || 'Failed to fetch position data'
     });
    }
   },
   error: function (xhr, status, error) {
    Swal.fire({
     icon: 'error',
     title: 'Error',
     text: 'Failed to fetch position data: ' + error
    });
   }
  });
 });

 // Edit Position Form Submit Handler
 $('#editPositionForm').on('submit', function (e) {
  e.preventDefault();

  // Create FormData object
  var formData = new FormData(this);

  // Show loading state
  Swal.fire({
   title: 'Updating Position...',
   allowOutsideClick: false,
   didOpen: () => {
    Swal.showLoading();
   }
  });

  $.ajax({
   url: 'main/api/updatePosition.php',
   type: 'POST',
   data: formData,
   processData: false,
   contentType: false,
   dataType: 'json',
   success: function (data) {
    if (data.success) {
     Swal.fire({
      icon: 'success',
      title: 'Success',
      text: data.message || 'Position updated successfully'
     }).then(() => {
      location.reload();
     });
    } else {
     Swal.fire({
      icon: 'error',
      title: 'Error',
      text: data.message || 'Failed to update position'
     });
    }
   },
   error: function (xhr, status, error) {
    console.error('Error details:', xhr.responseText);
    Swal.fire({
     icon: 'error',
     title: 'Error',
     text: 'Failed to update position: ' + (xhr.responseJSON?.message || error)
    });
   }
  });
 });

 // Delete Position Handler
 window.deletePosition = function (positionId) {
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
     url: 'main/api/deletePosition.php',
     type: 'GET',
     data: { id: positionId },
     dataType: 'json',
     success: function (response) {
      if (response.success) {
       Swal.fire({
        icon: 'success',
        title: 'Deleted!',
        text: response.message || 'Position deleted successfully'
       }).then(() => {
        location.reload();
       });
      } else {
       Swal.fire({
        icon: 'error',
        title: 'Cannot Delete Position',
        text: response.message || 'Failed to delete position'
       });
      }
     },
     error: function (xhr, status, error) {
      let errorMessage = 'Failed to delete position';

      try {
       // Try to parse the error response
       const response = JSON.parse(xhr.responseText);
       errorMessage = response.message || errorMessage;
      } catch (e) {
       // If parsing fails, use the generic error
       errorMessage += ': ' + error;
      }

      Swal.fire({
       icon: 'error',
       title: 'Error',
       text: errorMessage
      });

      console.error('Error details:', {
       status: xhr.status,
       response: xhr.responseText,
       error: error
      });
     }
    });
   }
  });
 };

});