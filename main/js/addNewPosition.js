document.addEventListener('DOMContentLoaded', function () {
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
      timer: 2000,
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

 $(document).ready(function () {
  // Edit Position Modal Handler
  $('#multi-filter-select').on('click', '.btn-primary', function () {
   const positionId = $(this).data('position-id');
   const positionName = $(this).data('position-name');
   const department = $(this).data('department');

   // Populate edit modal
   $('#editPositionId').val(positionId);
   $('#editPositionName').val(positionName);
   $('#editPositionDepartment').val(department);

   // Show the modal
   $('#editPositionModal').modal('show');
  });

  // Reset the modal when it's closed
  $('#editPositionModal').on('hidden', function () {
   $(this).find('form')[0].reset(); // Reset the form fields
   $('#editPositionErrorContainer').hide(); // Hide any error messages
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
 });

});

function deletePosition(positionId) {
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
    success: function (data) {
     if (data.success) {
      Swal.fire({
       icon: 'success',
       title: 'Deleted!',
       text: data.message || 'Position deleted successfully'
      }).then(() => {
       location.reload();
      });
     } else {
      Swal.fire({
       icon: 'error',
       title: 'Error',
       text: data.message || 'Failed to delete position'
      });
     }
    },
    error: function (xhr, status, error) {
     Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Failed to delete position: ' + error
     });
    }
   });
  }
 });
};