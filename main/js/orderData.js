document.addEventListener('DOMContentLoaded', function () {
 $(document).ready(function () {

  // Edit Order Modal Handler
  $('#editOrderModal').on('show.bs.modal', function (e) {
   const button = $(e.relatedTarget);
   const orderId = button.data('order-id');

   // Show loading state
   Swal.fire({
    title: 'Loading...',
    allowOutsideClick: false,
    didOpen: () => {
     Swal.showLoading();
    }
   });

   // Fetch order data
   $.ajax({
    url: '/api/getOrder.php',
    type: 'GET',
    data: { id_order: orderId },
    success: function (response) {
     const data = JSON.parse(response);

     // Populate form fields
     $('#edit_order_id').val(data.id_order);
     $('#edit_order_name').val(data.order_name);
     $('#edit_project_manager_id').val(data.project_manager_id);
     $('#edit_start_date').val(data.start_date);
     $('#edit_project_status').val(data.status);
     $('#edit_description').val(data.description);

     // Check if worker_id exists and is not null before setting
     if (data.worker_id) {
      $('#edit_worker_id').val(data.worker_id);
     } else {
      $('#edit_worker_id').val(''); // Reset to default
     }

     Swal.close();
    },
    error: function () {
     Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Failed to fetch order data'
     });
    }
   });
  });

  // Delete Order Handler
  window.deleteOrder = function (orderId) {
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
      url: '/api/deleteOrder.php',
      type: 'POST',
      data: { id_order: orderId },
      success: function (response) {
       const data = JSON.parse(response);
       if (data.success) {
        Swal.fire(
         'Deleted!',
         'Order has been deleted.',
         'success'
        ).then(() => {
         // Reload the page or remove the row from table
         location.reload();
        });
       } else {
        Swal.fire(
         'Error!',
         data.message || 'Failed to delete order',
         'error'
        );
       }
      },
      error: function () {
       Swal.fire(
        'Error!',
        'Failed to delete order',
        'error'
       );
      }
     });
    }
   });
  };

  // Add Order Form Submit Handler
  $('#addOrderForm').on('submit', function (e) {
   e.preventDefault();

   Swal.fire({
    title: 'Adding Order...',
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
       text: 'Order added successfully'
      }).then(() => {
       location.reload();
      });
     } else {
      Swal.fire({
       icon: 'error',
       title: 'Error',
       text: data.message || 'Failed to add order'
      });
     }
    },
    error: function () {
     Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Failed to add order'
     });
    }
   });
  });

  // Edit Order Form Submit Handler
  $('#editOrderForm').on('submit', function (e) {
   e.preventDefault();

   Swal.fire({
    title: 'Updating Order...',
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
       text: 'Order updated successfully'
      }).then(() => {
       location.reload();
      });
     } else {
      Swal.fire({
       icon: 'error',
       title: 'Error',
       text: data.message || 'Failed to update order'
      });
     }
    },
    error: function () {
     Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Failed to update order'
     });
    }
   });
  });
 });

});