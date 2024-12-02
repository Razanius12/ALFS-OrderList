document.addEventListener('DOMContentLoaded', function () {
 $(document).ready(function () {
     // Handler untuk menambahkan kantor baru
     $('#addAlfOffices form').on('submit', function (event) {
         event.preventDefault(); // Mencegah pengiriman form secara default

         const nameCityDistrict = $('input[name="name_city_district"]').val();
         const linkEmbed = $('input[name="link_embed"]').val();

         // Validasi input
         if (!nameCityDistrict || !linkEmbed) {
             Swal.fire({
                 icon: 'warning',
                 title: 'Warning',
                 text: 'All fields are required!'
             });
             return;
         }

         // Kirim data untuk menambahkan kantor baru
         $.ajax({
             url: 'main/api/addAlfOffices.php', // URL endpoint untuk menambahkan data kantor
             type: 'POST',
             data: {
                 name_city_district: nameCityDistrict,
                 link_embed: linkEmbed
             },
             dataType: 'json',
             success: function (response) {
                 if (response.success) {
                     Swal.fire({
                         icon: 'success',
                         title: 'Success',
                         text: 'Office added successfully!'
                     }).then(() => {
                         location.reload(); // Reload halaman setelah menambahkan kantor
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
                     text: 'Failed to add office: ' + error
                 });
             }
         });
     });

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
                 text: 'This action cannot be undone!',
                 icon: 'warning',
                 showCancelButton: true,
                 confirmButtonText: 'Yes, delete it!',
                 cancelButtonText: 'No, cancel!'
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