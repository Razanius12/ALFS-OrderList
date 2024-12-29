document.addEventListener('DOMContentLoaded', function () {
 $(document).ready(function () {

  // Function to get query parameters from the URL
  function getQueryParam(param) {
   const urlParams = new URLSearchParams(window.location.search);
   return urlParams.get(param);
  }

  // Get the name_worker parameter from the URL
  const nameWorker = getQueryParam('name_worker');

  if ($.fn.DataTable) {
   const table = $('#order-data-table').DataTable({
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

   // If name_worker is present, search the DataTable
   if (nameWorker) {
    table.search(nameWorker).draw(); // Search for the name_worker value
   }
  }

  // Function to set current date and time for start_date
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

   // Set the value of the start_date input
   $('#start_date').val(formattedDateTime).prop('readonly', true);

   // Calculate and set the default deadline (3 days later)
   setDeadline(formattedDateTime);
  }

  // Function to calculate and set deadline 3 days after start_date
  function setDeadline(startDateTime) {
   // Parse startDateTime into a Date object
   const startDate = new Date(startDateTime);

   // Add 3 days to the start date
   startDate.setDate(startDate.getDate() + 3);

   // Format the new date for datetime-local input
   const year = startDate.getFullYear();
   const month = String(startDate.getMonth() + 1).padStart(2, '0');
   const day = String(startDate.getDate()).padStart(2, '0');

   // Create datetime-local format (YYYY-MM-DDTHH:mm)
   const formattedDeadline = `${year}-${month}-${day}`;

   // Set the value of the deadline input
   $('#deadline').val(formattedDeadline);
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

      const formattedName = data.order_name
       ? data.order_name
        .replace(/\\r\\n|\\r|\\n/g, '\n')
        .replace(/\\"/g, '"')
        .replace(/\\'/g, "'")
       : '';

      $('#edit_order_name').val(formattedName);

      const formattedDescription = data.description
       ? data.description
        .replace(/\\r\\n|\\r|\\n/g, '\n')
        .replace(/\\"/g, '"')
        .replace(/\\'/g, "'")
       : '';

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

      $('#edit_deadline').val(data.deadline);

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

   // Create FormData object
   var formData = new FormData(this);

   // Show loading state
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
    data: formData,
    processData: false,  // Important for FormData
    contentType: false,  // Important for FormData
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
      text: 'Failed to add order: ' + (xhr.responseText || error)
     });
    }
   });
  });

  // Optional: Add file input validation
  $('input[name="references[]"]').on('change', function () {
   const files = this.files;
   const maxFiles = 10;
   const maxSize = 5 * 1024 * 1024; // 5MB
   const allowedTypes = [
    'image/jpeg', // .jpg, .jpeg
    'image/png',  // .png
    'image/gif',  // .gif
    'image/svg+xml', // .svg
    'application/postscript', // .ai
    'image/vnd.adobe.photoshop', // .psd
    'application/cdr', // .cdr
    'application/pdf', // .pdf
    'image/webp' // .webp
   ];

   if (files.length > maxFiles) {
    Swal.fire({
     icon: 'error',
     title: 'Too many files',
     text: `Maximum ${maxFiles} files allowed`
    });
    this.value = '';
    return;
   }

   for (let i = 0; i < files.length; i++) {
    if (files[i].size > maxSize) {
     Swal.fire({
      icon: 'error',
      title: 'File too large',
      text: `${files[i].name} exceeds 5MB limit`
     });
     this.value = '';
     return;
    }

    if (!allowedTypes.includes(files[i].type)) {
     Swal.fire({
      icon: 'error',
      title: 'Invalid file type',
      text: `${files[i].name} is not a supported file type`
     });
     this.value = '';
     return;
    }
   }
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

// Global variable to store current order ID and modal instance
let currentOrderId;
let attachmentModal;

function viewAttachments(orderId) {
 currentOrderId = orderId;
 attachmentModal = new bootstrap.Modal(document.getElementById('viewAttachmentsModal'));
 const attachmentsList = document.getElementById('attachmentsList');
 const referencesList = document.getElementById('referencesList');
 const orderDescription = document.getElementById('orderDescription');

 attachmentsList.innerHTML = '';
 referencesList.innerHTML = '';
 orderDescription.innerHTML = 'Loading...';

 fetchAttachments();
}

function fetchAttachments() {
 fetch(`main/api/getOrderAttachments.php?order_id=${currentOrderId}`)
  .then(response => {
   if (!response.ok) {
    throw new Error(`HTTP error! status: ${response.status}`);
   }
   return response.text(); // Get response as text
  })
  .then(text => {
   try {
    return JSON.parse(text); // Try to parse the text as JSON
   } catch (e) {
    console.error('JSON Parse Error:', text);
    throw new Error('Invalid JSON response from server');
   }
  })
  .then(data => {
   if (data.success) {
    const modalTitle = document.getElementById('attachmentModalTitle');
    const orderDescription = document.getElementById('orderDescription');
    const attachmentsList = document.getElementById('attachmentsList');
    const referencesList = document.getElementById('referencesList');

    modalTitle.textContent = data.order_name;

    const formattedDescription = data.description
     ? data.description
      .replace(/\\r\\n|\\r|\\n/g, '<br>')
      .replace(/\\"/g, '"')
      .replace(/\\'/g, "'")
     : 'No description available';

    orderDescription.innerHTML = formattedDescription;

    // Handle attachments
    if (data.attachments && data.attachments.length > 0) {
     attachmentsList.innerHTML = '';
     data.attachments.forEach((file, index) => {
      if (file.name) {
       const fileElement = document.createElement('div');
       fileElement.className = 'list-group-item d-flex justify-content-between align-items-center';

       const contentDiv = document.createElement('div');
       // Add attachment column information
       const attachmentColumn = `atch${index + 1}`;

       if (file.path.match(/\.(jpeg|jpg|png|gif|svg|webp|PNG)$/)) {
        contentDiv.innerHTML = `
                        <a href="${file.path}" target="_blank">
                            <img src="${file.path}" alt="Attachment Image" class="img-thumbnail" 
                                 style="max-width: 100px; max-height: 100px;">
                            <div class="preview-caption">${file.name}</div>
                        </a>`;
       } else {
        contentDiv.innerHTML = `<a href="${file.path}" target="_blank">${file.name}</a>`;
       }

       const deleteButton = document.createElement('button');
       deleteButton.className = 'btn btn-danger btn-sm';
       deleteButton.innerHTML = '<i class="fas fa-trash"></i>';
       deleteButton.onclick = () => deleteAttachment(attachmentColumn, 'attachment');

       fileElement.appendChild(contentDiv);
       fileElement.appendChild(deleteButton);
       attachmentsList.appendChild(fileElement);
      }
     });
    } else {
     attachmentsList.innerHTML = '<div class="list-group-item">No attachments available</div>';
    }

    // Handle references
    if (data.references && data.references.length > 0) {
     referencesList.innerHTML = '';
     data.references.forEach((file, index) => {
      if (file.name) {
       const fileElement = document.createElement('div');
       fileElement.className = 'list-group-item d-flex justify-content-between align-items-center';

       const contentDiv = document.createElement('div');
       // Add reference column information
       const referenceColumn = `atch${index + 1}`;

       if (file.path.match(/\.(jpeg|jpg|png|gif|svg|webp|PNG)$/)) {
        contentDiv.innerHTML = `
                        <a href="${file.path}" target="_blank">
                            <img src="${file.path}" alt="Reference Image" class="img-thumbnail" 
                                 style="max-width: 100px; max-height: 100px;">
                            <div class="preview-caption">${file.name}</div>
                        </a>`;
       } else {
        contentDiv.innerHTML = `<a href="${file.path}" target="_blank">${file.name}</a>`;
       }

       const deleteButton = document.createElement('button');
       deleteButton.className = 'btn btn-danger btn-sm';
       deleteButton.innerHTML = '<i class="fas fa-trash"></i>';
       deleteButton.onclick = () => deleteAttachment(referenceColumn, 'reference');

       fileElement.appendChild(contentDiv);
       fileElement.appendChild(deleteButton);
       referencesList.appendChild(fileElement);
      }
     });
    } else {
     referencesList.innerHTML = '<div class="list-group-item">No references available</div>';
    }

    // Add button to add more references
    if (data.references.length < 10) {
     const buttonWrapper = document.createElement('div');
     buttonWrapper.className = 'd-flex justify-content-end mt-2';

     const addReferenceButton = document.createElement('button');
     addReferenceButton.className = 'btn btn-primary btn-sm';
     addReferenceButton.innerHTML = 'Add Reference';
     addReferenceButton.onclick = () => addReference();

     buttonWrapper.appendChild(addReferenceButton);
     referencesList.appendChild(buttonWrapper);
    }

    attachmentModal.show();
   } else {
    throw new Error(data.message || 'Failed to fetch attachments');
   }
  })
  .catch(error => {
   Swal.fire({
    icon: 'error',
    title: 'Error',
    text: `Failed to load attachments: ${error.message}`,
   });
  });
}

function addReference() {
 const input = document.createElement('input');
 input.type = 'file';
 input.accept = 'image/jpeg,image/png,image/gif,image/svg+xml,application/postscript,image/vnd.adobe.photoshop,application/cdr';
 input.onchange = function () {
  const file = this.files[0];
  if (file) {
   const formData = new FormData();
   formData.append('order_id', currentOrderId); // Ensure order_id is included
   formData.append('reference', file);

   Swal.fire({
    title: 'Uploading Reference...',
    allowOutsideClick: false,
    didOpen: () => {
     Swal.showLoading();
    }
   });

   fetch('main/api/addReference.php', {
    method: 'POST',
    body: formData
   })
    .then(response => response.json())
    .then(data => {
     Swal.close();
     if (data.success) {
      Swal.fire({
       icon: 'success',
       title: 'Success',
       text: 'Reference added successfully'
      }).then(() => {
       fetchAttachments();
       attachmentModal.show();
      });
     } else {
      throw new Error(data.message);
     }
    })
    .catch(error => {
     Swal.fire({
      icon: 'error',
      title: 'Error',
      text: `Failed to add reference: ${error.message}`,
     });
    });
  }
 };
 input.click();
}

function deleteAttachment(column, type) {
 Swal.fire({
  title: 'Are you sure?',
  text: `Do you want to delete this ${type}?`,
  icon: 'warning',
  showCancelButton: true,
  confirmButtonColor: '#d33',
  cancelButtonColor: '#3085d6',
  confirmButtonText: 'Yes, delete it!'
 }).then((result) => {
  if (result.isConfirmed) {
   attachmentModal.hide(); // Hide modal before making request

   const loadingSwal = Swal.fire({
    title: 'Deleting...',
    text: `Deleting ${type}...`,
    allowOutsideClick: false,
    didOpen: () => {
     Swal.showLoading();
    }
   });

   fetch('main/api/deleteAttachment.php', {
    method: 'POST',
    headers: {
     'Content-Type': 'application/json',
    },
    body: JSON.stringify({
     order_id: currentOrderId,
     attachment_column: column,
     type: type
    })
   })
    .then(response => response.json())
    .then(data => {
     loadingSwal.close();
     if (data.success) {
      Swal.fire({
       icon: 'success',
       title: 'Deleted!',
       text: `The ${type} has been deleted.`,
      }).then(() => {
       attachmentModal.hide();
       fetchAttachments();
       attachmentModal.show();
      });
     } else {
      throw new Error(data.message);
     }
    })
    .catch(error => {
     loadingSwal.close();
     Swal.fire({
      icon: 'error',
      title: 'Error',
      text: `Failed to delete ${type}: ${error.message}`,
     }).then(() => {
      attachmentModal.show();
     });
    });
  }
 });
}

function deleteAllAttachments(type = 'all') {
 let title = '';
 let text = '';

 switch (type) {
  case 'all':
   title = 'Delete All Files?';
   text = 'Are you sure you want to delete all attachments and references? This cannot be undone!';
   break;
  case 'attachments':
   title = 'Delete All Attachments?';
   text = 'Are you sure you want to delete all attachments? This cannot be undone!';
   break;
  case 'references':
   title = 'Delete All References?';
   text = 'Are you sure you want to delete all references? This cannot be undone!';
   break;
 }

 Swal.fire({
  title: title,
  text: text,
  icon: 'warning',
  showCancelButton: true,
  confirmButtonColor: '#d33',
  cancelButtonColor: '#3085d6',
  confirmButtonText: 'Yes, delete them!'
 }).then((result) => {
  if (result.isConfirmed) {
   attachmentModal.hide();

   const loadingSwal = Swal.fire({
    title: 'Deleting...',
    text: 'Deleting files...',
    allowOutsideClick: false,
    didOpen: () => {
     Swal.showLoading();
    }
   });

   fetch('main/api/deleteAttachment.php', {
    method: 'POST',
    headers: {
     'Content-Type': 'application/json',
    },
    body: JSON.stringify({
     order_id: currentOrderId,
     delete_all: true,
     type: type
    })
   })
    .then(response => response.json())
    .then(data => {
     loadingSwal.close();
     if (data.success) {
      Swal.fire({
       icon: 'success',
       title: 'Deleted!',
       text: 'The files have been deleted.',
      }).then(() => {
       attachmentModal.hide();
       fetchAttachments();
       attachmentModal.show();
      });
     } else {
      throw new Error(data.message);
     }
    })
    .catch(error => {
     loadingSwal.close();
     Swal.fire({
      icon: 'error',
      title: 'Error',
      text: `Failed to delete files: ${error.message}`,
     }).then(() => {
      // Show modal again in case of error
      attachmentModal.show();
     });
    });
  }
 });
}