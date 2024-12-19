document.addEventListener('DOMContentLoaded', function () {
 const addAlfOfficesForm = document.getElementById('addAlfOfficesForm');

 if (addAlfOfficesForm) {
  addAlfOfficesForm.addEventListener('submit', function (e) {
   e.preventDefault();

   const formData = new FormData(this);
   const linkEmbed = formData.get('link_embed');

   // Regular expression for validating iframe tag
   const iframeRegex = /^<iframe[^>]*src=["']https?:\/\/[^"']+["'][^>]*><\/iframe>$/;

   // Regular expression for validating URLs
   const urlRegex = /^(https?:\/\/)?([\w\-]+\.)+[\w\-]+(\/[\w\-._~:/?#[\]@!$&'()*+,;=%]*)?$/;

   // Check if input matches the iframe format or a valid URL
   if (!linkEmbed || (!iframeRegex.test(linkEmbed) && !urlRegex.test(linkEmbed))) {
    Swal.fire({
     icon: 'error',
     title: 'Invalid Input',
     text: 'The Link Embed field must contain a valid iframe or URL.',
    });
    return; // Stop form submission
   }

   // Submit data to the API
   fetch('main/api/addAlfOffices.php', {
    method: 'POST',
    body: formData,
   })
    .then(response => response.json())
    .then(data => {
     if (data.success) {
      Swal.fire({
       icon: 'success',
       title: 'Success!',
       text: data.message,
      }).then(() => location.reload()); // Reload the page on success
     } else {
      Swal.fire({
       icon: 'error',
       title: 'Error!',
       text: data.message,
      });
     }
    })
    .catch(error => {
     console.error('Error:', error);
     Swal.fire({
      icon: 'error',
      title: 'Network Error',
      text: 'Could not complete the request.',
     });
    });
  });
 }

 /**
  * Helper function to validate a URL
  */
 function isValidURL(string) {
  try {
   new URL(string);
   return true;
  } catch (_) {
   return false;
  }
 }

 $(document).ready(function () {

  // Handler untuk menyimpan perubahan saat mengedit kantor
  $('#editAlfOffices form').on('submit', function (event) {
   event.preventDefault(); // Prevent default form submission

   const idMaps = $('#edit_id_maps').val();
   const nameCityDistrict = $('#edit_name_city_district').val();
   const linkEmbed = $('#edit_link_embed').val().trim();

   // Regular expression for validating iframe tag
   const iframeRegex = /^<iframe[^>]*src=["']https?:\/\/[^"']+["'][^>]*><\/iframe>$/;

   // Regular expression for validating Google Maps share links and general URLs
   const googleMapsShareLinkRegex = /^https:\/\/maps\.app\.goo\.gl\/.+$/;
   const urlRegex = /^(https?:\/\/)?([\w\-]+\.)+[\w\-]+(\/[\w\-._~:/?#[\]@!$&'()*+,;=%]*)?$/;

   // Validation logic
   if (!linkEmbed) {
    Swal.fire({
     icon: 'error',
     title: 'Invalid Input',
     text: 'The Link Embed field cannot be empty.',
    });
    return; // Stop submission
   }

   if (!iframeRegex.test(linkEmbed) && !googleMapsShareLinkRegex.test(linkEmbed) && !urlRegex.test(linkEmbed)) {
    Swal.fire({
     icon: 'error',
     title: 'Invalid Input',
     text: 'The Link Embed field must contain a valid iframe, Google Maps share link, or URL.',
    });
    return; // Stop submission
   }

   // Send the updated data to the server
   $.ajax({
    url: 'main/api/updateAlfOffices.php', // Endpoint for updating office
    type: 'POST',
    data: {
     id_maps: idMaps,
     name_city_district: nameCityDistrict,
     link_embed: linkEmbed
    },
    dataType: 'json',
    success: function (response) {
     if (response.success) {
      Swal.fire({
       icon: 'success',
       title: 'Success',
       text: response.message
      }).then(() => {
       location.reload(); // Reload the page after updating office
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


  // Mengisi modal edit dengan data yang ada
  document.querySelectorAll('.edit-office').forEach(button => {
   button.addEventListener('click', function () {
    // Ambil data dari atribut
    const id = this.getAttribute('data-id');
    const city = this.getAttribute('data-city');
    const link = this.getAttribute('data-link');

    // Set nilai dalam modal
    document.getElementById('edit_id_maps').value = id;
    document.getElementById('edit_name_city_district').value = city;
    document.getElementById('edit_link_embed').value = link;
   });
  });

  // Handler untuk menghapus kantor
  document.querySelectorAll('.delete-office').forEach(button => {
   button.addEventListener('click', function () {
    const idMaps = this.getAttribute('data-id');

    // Konfirmasi penghapusan
    Swal.fire({
     title: 'Are you sure?',
     text: "You won't be able to revert this!",
     icon: 'warning',
     showCancelButton: true,
     confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
     if (result.isConfirmed) {
      // Kirim permintaan hapus ke server
      $.ajax({
       url: 'main/api/deleteAlfOffices.php', // Endpoint untuk menghapus kantor
       type: 'POST',
       data: {
        id_maps: idMaps
       },
       dataType: 'json',
       success: function (response) {
        if (response.success) {
         Swal.fire({
          icon: 'success',
          title: 'Deleted!',
          text: response.message
         }).then(() => {
          location.reload(); // Reload halaman setelah menghapus kantor
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
         text: 'Failed to delete office: ' + error
        });
       }
      });
     }
    });
   });
  });
 });

});