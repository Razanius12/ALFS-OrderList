document.addEventListener('DOMContentLoaded', function () {
 $.fn.dataTable.ext.type.order['remaining-time-pre'] = function (data) {
  // Convert the remaining time text to a numeric value for sorting
  if (data.includes('Overdue')) {
   return -1; // Treat overdue as the lowest priority
  } else if (data.includes('Less than 1 day left')) {
   return 0; // Treat as 0 days left
  } else {
   const match = data.match(/(\d+) days left/);
   return match ? parseInt(match[1], 10) : Infinity; // Treat as a large number if not matched
  }
 };

 // DataTable initialization, specify the column type
 $('#task-table').DataTable({
  "pageLength": 10,
  "order": [[2, "asc"]],
  "columnDefs": [
   {
    targets: 2, // The Remaining Time column
    type: 'remaining-time' // Use the custom sorting type
   },
   {
    targets: 0,
    visible: false
   }
  ],
  responsive: true,
  language: {
   search: "_INPUT_",
   searchPlaceholder: "Search tasks...",
   lengthMenu: "Show _MENU_ entries"
  }
 });
});

function showTaskDetails(button) {
 const taskId = button.getAttribute('data-task-id');
 const taskName = button.getAttribute('data-task-name');
 const taskDescription = button.getAttribute('data-task-description') || 'No description available';
 const taskStatus = button.getAttribute('data-task-status');

  // First replace escaped characters, then handle line breaks
  const formattedName = taskName
  .replace(/\\"/g, '"')              // Replace escaped quotes
  .replace(/\\'/g, "'")              // Replace escaped single quotes
  .replace(/\\n/g, '\n')             // Convert \n string to actual line break
  .replace(/\\r/g, '')               // Remove \r
  .split('\n')                       // Split by line breaks
  .join('<br>');                     // Join with HTML line breaks

 // First replace escaped characters, then handle line breaks
 const formattedDescription = taskDescription
  .replace(/\\"/g, '"')              // Replace escaped quotes
  .replace(/\\'/g, "'")              // Replace escaped single quotes
  .replace(/\\n/g, '\n')             // Convert \n string to actual line break
  .replace(/\\r/g, '')               // Remove \r
  .split('\n')                       // Split by line breaks
  .join('<br>');                     // Join with HTML line breaks

 // Fetch references and attachments
 fetch(`main/api/getOrderAttachments.php?order_id=${taskId}`)
  .then(response => response.json())
  .then(data => {
   if (data.success) {
    const referencesList = data.references.map(file => {
     if (file.path.match(/\.(jpeg|jpg|png|gif|svg|webp|PNG)$/)) {
      return `
        <a href="${file.path}" target="_blank">
         <img src="${file.path}" alt="Reference Image" class="img-thumbnail" style="max-height: 200px;">
        </a>
        `;
     } else {
      return `<a href="${file.path}" target="_blank">${file.name}</a>`;
     }
    }).join('<br>');

    // Populate modal content
    document.getElementById('taskDetailsModalLabel').innerText = formattedName;
    document.getElementById('taskDescription').innerHTML = formattedDescription;
    document.getElementById('taskStatus').innerText = taskStatus;
    document.getElementById('taskStatus').className = `badge bg-${getStatusBadgeClass(taskStatus)}`;
    document.getElementById('taskReferences').innerHTML = referencesList || 'No references available';

    // Show modal
    var taskDetailsModal = new bootstrap.Modal(document.getElementById('taskDetailsModal'));
    taskDetailsModal.show();
   } else {
    throw new Error(data.message || 'Failed to fetch references');
   }
  })
  .catch(error => {
   console.error('Error:', error);
   alert(`Failed to load references: ${error.message}`);
  });
}

function takeOrder(taskId) {
 Swal.fire({
  title: 'Take Order',
  text: "Are you sure you want to take this task?",
  icon: 'question',
  showCancelButton: true,
  confirmButtonColor: '#3085d6',
  cancelButtonColor: '#d33',
  confirmButtonText: 'Yes, take it!'
 }).then((result) => {
  if (result.isConfirmed) {
   fetch('main/api/takeOrder.php', {
    method: 'POST',
    headers: {
     'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `task_id=${taskId}`
   })
    .then(response => {
     if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
     }
     return response.text().then(text => {
      try {
       return JSON.parse(text);
      } catch (e) {
       console.error('JSON Parse Error:', text);
       throw new Error('Invalid JSON response from server');
      }
     });
    })
    .then(data => {
     if (data.success) {
      Swal.fire({
       title: 'Task Taken!',
       text: 'You have successfully taken the task.',
       icon: 'success',
       confirmButtonText: 'OK'
      }).then(() => {
       location.reload();
      });
     } else {
      Swal.fire({
       title: 'Error',
       text: data.message || 'Failed to take the task',
       icon: 'error',
       confirmButtonText: 'OK'
      });
     }
    })
    .catch(error => {
     console.error('Error:', error);
     Swal.fire({
      title: 'Error',
      text: error.message || 'An error occurred while taking the task.',
      icon: 'error',
      confirmButtonText: 'OK'
     });
    });
  }
 });
}

function markTaskComplete(taskId) {
 Swal.fire({
  title: 'Submit Task Completion',
  html: `
    <form id="taskCompletionForm" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label">Attachments (Max 10 files, 5MB each)</label>
        <input type="file" class="form-control" id="taskAttachments" name="attachments[]" multiple 
          accept=".jpg,.jpeg,.png,.gif,.svg,.ai,.psd,.cdr" max="10">
        <small class="text-muted">Allowed files: Images, SVG, AI, PSD, CDR (Max 5MB each)</small>
      </div>
    </form>
  `,
  showCancelButton: true,
  confirmButtonText: 'Submit',
  cancelButtonText: 'Cancel',
  preConfirm: () => {
   const files = document.getElementById('taskAttachments').files;
   if (files.length > 10) {
    Swal.showValidationMessage('Maximum 10 files allowed');
    return false;
   }
   if (files.length == 0) {
    Swal.fire({
     title: 'No Attachments',
     text: 'Please upload at least one attachment before completing the task.',
     icon: 'warning'
    });
    return;
   }

   for (let file of files) {
    if (file.size > 5 * 1024 * 1024) { // 5MB in bytes
     Swal.showValidationMessage(`File ${file.name} exceeds 5MB limit`);
     return false;
    }

    const allowedTypes = ['.jpg', '.jpeg', '.png', '.gif', '.svg', '.ai', '.psd', '.cdr'];
    const ext = '.' + file.name.split('.').pop().toLowerCase();
    if (!allowedTypes.includes(ext)) {
     Swal.showValidationMessage(`File ${file.name} has invalid type`);
     return false;
    }
   }
   return true;
  }
 }).then((result) => {
  if (result.isConfirmed) {
   const formData = new FormData(document.getElementById('taskCompletionForm'));
   formData.append('task_id', taskId);

   Swal.fire({
    title: 'Uploading...',
    html: 'Please wait while we upload your files',
    allowOutsideClick: false,
    didOpen: () => {
     Swal.showLoading();
    }
   });

   fetch('main/api/updateTask.php', {
    method: 'POST',
    body: formData
   })
    .then(response => response.json())
    .then(data => {
     if (data.success) {
      Swal.fire({
       title: 'Success!',
       text: 'Task completed and files uploaded successfully',
       icon: 'success'
      }).then(() => {
       location.reload();
      });
     } else {
      throw new Error(data.message || 'Failed to update task');
     }
    })
    .catch(error => {
     Swal.fire({
      title: 'Error',
      text: error.message,
      icon: 'error'
     });
    });
  }
 });
}

function getStatusBadgeClass(status) {
 switch (status.toUpperCase()) {
  case 'COMPLETED': return 'success';
  case 'IN_PROGRESS': return 'info';
  case 'PENDING': return 'warning';
  case 'CANCELLED': return 'danger';
  default: return 'secondary';
 }
}