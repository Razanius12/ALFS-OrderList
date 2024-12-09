document.addEventListener('DOMContentLoaded', function () {

 const addAlfOfficesForm = document.getElementById('addAlfOfficesForm');

 if (addAlfOfficesForm) {
  addAlfOfficesForm.addEventListener('submit', function (e) {
   e.preventDefault();

   const formData = new FormData(this);

   fetch('main/api/addAlfOffices.php', {
    method: 'POST',
    body: formData
   })
    .then(response => response.json())
    .then(data => {
     if (data.success) {
      // Success handling
      Swal.fire({
       icon: 'success',
       title: 'Success!',
       text: data.message,
       timer: 1500
      }).then(() => {
       // Reload the page or dynamically add the new office
       location.reload();
      });
     } else {
      // Error handling
      Swal.fire({
       icon: 'error',
       title: 'Error!',
       text: data.message
      });
     }
    })
    .catch(error => {
     console.error('Error:', error);
     Swal.fire({
      icon: 'error',
      title: 'Network Error',
      text: 'Could not complete the request'
     });
    });
  });
 }

 $(document).ready(function () {

  // Handler untuk menyimpan perubahan saat mengedit kantor
  $('#editAlfOffices form').on('submit', function (event) {
   event.preventDefault(); // Mencegah pengiriman form secara default

   const idMaps = $('#edit_id_maps').val();
   const nameCityDistrict = $('#edit_name_city_district').val();
   const linkEmbed = $('#edit_link_embed').val();

   // Validasi input
   if (!idMaps || !nameCityDistrict || !linkEmbed) {
    Swal.fire({
     icon: 'warning',
     title: 'Warning',
     text: 'All fields are required!'
    });
    return;
   }

   // Kirim data yang diperbarui ke server
   $.ajax({
    url: 'main/api/updateAlfOffices.php', // URL endpoint untuk memperbarui data kantor
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
       text: 'Office updated successfully!'
      }).then(() => {
       location.reload(); // Reload halaman setelah memperbarui kantor
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