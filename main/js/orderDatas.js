document.addEventListener('DOMContentLoaded', function () {
 if ($.fn.DataTable) {
  $('#order-data-table').DataTable({
   "pageLength": 10,
   "order": [[0, "desc"]],
   responsive: true,
   language: {
    search: "_INPUT_",
    searchPlaceholder: "Search orders...",
    lengthMenu: "Show _MENU_ entries"
   },
   columnDefs: [
    {
     targets: 0,
     visible: false
    }
   ]
  });
 }
 $(document).ready(function () {

  // Function to set current date and time
  function setCurrentDateTime() {
   // Get current date and time
   const now = new Date();

   // Format date to match datetime-local input requirements
   const year = now.getFullYear();
   const month = String(now.getMonth() + 1).padStart(2, '0');
   const day = String(now.getDate()).padStart(2, '0');
   const hours = String(now.getHours()).padStart(2, '0');
   const minutes = String(now.getMinutes()).padStart(2, '0');

   // Create datetime-local format (YYYY-MM-DDTHH:mm)
   const formattedDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;

   // Set the value of the input
   $('#start_date').val(formattedDateTime);
  }

  // Call the function when the page loads
  setCurrentDateTime();

  // Auto-select Project Manager based on session
  function initializeProjectManagerSelects() {
   const isAdmin = window.isAdmin || false; // Get from PHP variable
   const autoSelectProjectManager = window.autoSelectProjectManager || null; // Get from PHP variable

   if (isAdmin && autoSelectProjectManager) {
    // For Add Order Modal
    const addModalSelect = document.querySelector('#addOrderModal select[name="project_manager_id"]');
    if (addModalSelect) {
     addModalSelect.value = autoSelectProjectManager;
     addModalSelect.disabled = true;
    }

    // For Edit Order Modal
    const editModalSelect = document.querySelector('#editOrderModal select[name="project_manager_id"]');
    if (editModalSelect) {
     editModalSelect.value = autoSelectProjectManager;
     editModalSelect.disabled = true;
    }
   }
  }

  // Call the initialization function
  initializeProjectManagerSelects();

  // Function to calculate the number of rows based on the number of lines in the text
  function calculateRows(text) {
   const lines = text.split('\n').length; // Count the number of lines
   return lines; // Return the number of lines as the number of rows
  }

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
    url: 'main/api/getOrder.php',
    type: 'GET',
    data: { order_id: orderId },
    dataType: 'json',
    success: function (response) {
     if (response.success) {
      const data = response.data;
      const workers = response.workers;

      // Populate form fields
      $('#edit_order_id').val(data.id_order);
      $('#edit_order_name').val(data.order_name);

      // Format description
      const formattedDescription = data.description
      .replace(/\\r\\n|\\r|\\n/g, '\n')  // Replace line breaks for textarea
      .replace(/\\"/g, '"')              // Replace escaped quotes
      .replace(/\\'/g, "'");             // Replace escaped single quotes

      $('#edit_description').val(formattedDescription);

      // Calculate and set the number of rows based on the description length
      const rows = calculateRows(formattedDescription);
      $('#edit_description').attr('rows', rows);

      $('#edit_order_price').val(data.order_price);

      // Format and set the start date
      if (data.start_date) {
       const startDate = new Date(data.start_date); // Assuming this is in UTC
       const localStartDate = new Date(startDate.getTime() - (startDate.getTimezoneOffset() * 60000)); // Adjust for timezone offset
       const formattedStartDate = localStartDate.toISOString().slice(0, 16); // Format to YYYY-MM-DDTHH:MM
       $('#edit_start_date').val(formattedStartDate);
      } else {
       setCurrentDateTime(); // Optionally set current date if no start_date
      }

      // Populate status dropdown
      $('#edit_status').val(data.status);

      // Populate project manager dropdown
      $('#edit_project_manager').val(data.project_manager_id);

      // Populate worker dropdown
      const workerDropdown = $('#edit_assigned_worker');
      workerDropdown.empty();

      // Add a default option
      workerDropdown.append(
       '<option value="">Select Worker</option>'
      );

      // Add current worker (if assigned) as a selected option
      if (data.worker_id) {
       workerDropdown.append(
        `<option value="${data.worker_id}" selected>
       ${data.worker_name}
       </option>`
       );
      }

      // Add available workers
      workers.forEach(function (worker) {
       workerDropdown.append(
        `<option value="${worker.id_worker}">
      ${worker.name_worker}
     </option>`
       );
      });

      Swal.close();
     } else {
      Swal.fire({
       icon: 'error',
       title: 'Error',
       text: response.message || 'Failed to fetch order data'
      });
     }
    },
    error: function (xhr, status, error) {
     Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Failed to fetch order data: ' + error
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
      url: 'main/api/deleteOrder.php',
      type: 'POST',
      dataType: 'json',
      data: { id_order: orderId },
      success: function (data) {
       if (data.success) {
        Swal.fire(
         'Deleted!',
         'Order has been deleted.',
         'success'
        ).then(() => {
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
    dataType: 'json',
    data: $(this).serialize(),
    success: function (data) {
     if (data.success) {
      Swal.fire({
       icon: 'success',
       title: 'Success',
       text: data.message || 'Order added successfully'
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
    error: function (xhr, status, error) {
     // More detailed error handling
     console.error('AJAX Error:', status, error);
     console.log('Response Text:', xhr.responseText);

     Swal.fire({
      icon: 'error',
      title: 'Error',
      text: xhr.responseText || 'Failed to add order'
     });
    }
   });
  });

  // Edit Order Form Submit Handler
  $('#editOrderForm').on('submit', function (e) {
   e.preventDefault();

   // Create FormData object to handle file uploads and more complex form data
   var formData = new FormData(this);

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
    data: formData,
    processData: false,  // Important for FormData
    contentType: false,  // Important for FormData
    dataType: 'json',   // Explicitly tell jQuery to expect JSON
    success: function (data) {
     if (data.success) {
      Swal.fire({
       icon: 'success',
       title: 'Success',
       text: data.message || 'Order updated successfully'
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
    error: function (xhr, status, error) {
     console.error('Error details:', xhr.responseText);

     // Check if the response indicates that the order can be deleted
     if (xhr.responseText.includes('delete the order directly')) {
      Swal.fire({
       icon: 'warning',
       title: 'Order Pending',
       text: xhr.responseJSON?.message || 'Failed to update order',
       showCancelButton: true,
       confirmButtonText: 'Delete Order',
       cancelButtonText: 'Cancel'
      }).then((result) => {
       if (result.isConfirmed) {
        // Retrieve order ID from the hidden input field in the edit order modal
        var orderId = $('#edit_order_id').val();
        deleteOrder(orderId); // Pass the order ID to the deleteOrder function
       }
      });
     } else {
      // Handle other types of errors (like trying to update to "completed")
      Swal.fire({
       icon: 'error',
       title: 'Error',
       text: 'Failed to update order: ' + (xhr.responseJSON?.message || error)
      });
     }
    }

   });
  });

 });

});