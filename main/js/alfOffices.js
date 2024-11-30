document.addEventListener('DOMContentLoaded', function () {
 $(document).ready(function () {
  // Change the selector to use the form's ID specifically
  $('form#addAlfOffices').on('submit', function (e) {
   e.preventDefault();

   // Ensure we're using the actual form element
   var form = $(this)[0]; // Get the DOM element
   var formData = new FormData(form);

   Swal.fire({
    title: 'Adding Map Entry...',
    allowOutsideClick: false,
    didOpen: () => {
     Swal.showLoading();
    }
   });

   $.ajax({
    url: $(this).attr('action'),
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
       text: data.message || 'Map entry added successfully'
      }).then(() => {
       location.reload();
      });
     } else {
      Swal.fire({
       icon: 'error',
       title: 'Error',
       text: data.message || 'Failed to add map entry'
      });
     }
    },
    error: function (xhr, status, error) {
     console.error('AJAX Error:', status, error);
     console.error('Response Text:', xhr.responseText);

     try {
      var errorResponse = JSON.parse(xhr.responseText);
      Swal.fire({
       icon: 'error',
       title: 'Error',
       text: errorResponse.message || 'Failed to add map entry'
      });
     } catch (e) {
      Swal.fire({
       icon: 'error',
       title: 'Error',
       text: xhr.responseText || 'Failed to add map entry'
      });
     }
    }
   });
  });

  // Edit ALF Office Modal Handler
  $('#editAlfOffices').on('show.bs.modal', function (e) {
   const button = $(e.relatedTarget);
   const officeId = button.data('id_maps');

   // Show loading state
   Swal.fire({
    title: 'Loading Office Details...',
    allowOutsideClick: false,
    didOpen: () => {
     Swal.showLoading();
    }
   });

   // Fetch office data
   $.ajax({
    url: 'main/api/getAlfOffices.php', // Modify to accept specific office ID
    type: 'GET',
    data: { id_maps: officeId },
    dataType: 'json',
    success: function (response) {
     if (response.success) {
      const data = response.data;

      // Populate form fields
      $('#id_maps').val(data.id_maps);
      $('#name_city_district').val(data.name_city_district);
      $('#link_embed').val(data.link_embed);

      Swal.close();
     } else {
      Swal.fire({
       icon: 'error',
       title: 'Error',
       text: response.message || 'Failed to fetch office data'
      });
     }
    },
    error: function (xhr, status, error) {
     Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Failed to fetch office data: ' + error
     });
    }
   });
  });

  // Form Submission Handler
  $('#editAlfOffices').on('submit', function (e) {
   e.preventDefault();

   const embedLink = $('#edit_link_embed').val().trim();

   if (!validateGoogleMapsEmbedLink(embedLink)) {
    Swal.fire({
     icon: 'error',
     title: 'Invalid Embed Link',
     text: 'Please use a valid Google Maps embed link'
    });
    return;
   }

   $.ajax({
    url: 'main/api/updateAlfOffices.php',
    type: 'POST',
    data: $(this).serialize(),
    dataType: 'json',
    success: function (response) {
     if (response.success) {
      Swal.fire({
       icon: 'success',
       title: 'Updated',
       text: response.message
      }).then(() => {
       $('#editOfficeModal').modal('hide');
       // Optional: Refresh office list
       loadAlfOffices();
      });
     } else {
      Swal.fire({
       icon: 'error',
       title: 'Error',
       text: response.message
      });
     }
    },
    error: function (xhr, status, error) {
     Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Failed to update office: ' + error
     });
    }
   });
  });

  // Delete Office Handler
  $('.delete-office').on('click', function (e) {
   e.preventDefault();

   const id = $(this).data('id');

   Swal.fire({
    title: 'Are you sure?',
    text: 'Do you want to delete this ALF Office?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, delete it!'
   }).then((result) => {
    if (result.isConfirmed) {
     $.ajax({
      url: 'main/api/deleteAlfOffices.php',
      method: 'POST',
      data: { id_maps: id },  // Note the change to id_maps
      dataType: 'json',
      success: function (response) {
       if (response.success) {
        Swal.fire({
         icon: 'success',
         title: 'Deleted!',
         text: response.message
        }).then(() => {
         location.reload();
        });
       } else {
        Swal.fire({
         icon: 'error',
         title: 'Error',
         text: response.message
        });
       }
      },
      error: function () {
       Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Something went wrong!'
       });
      }
     });
    }
   });
  });

 });
});