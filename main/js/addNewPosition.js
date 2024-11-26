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
      $('#editPositionModal').modal('hide'); // Hide modal after success
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