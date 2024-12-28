document.addEventListener('DOMContentLoaded', function () {
 if ($.fn.DataTable) {
  $('#task-table').DataTable({
   "pageLength": 10,
   "order": [[0, "desc"]],
   responsive: true,
   language: {
    search: "_INPUT_",
    searchPlaceholder: "Search tasks...",
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
});

function showTaskDetails(button) {
 const taskId = button.getAttribute('data-task-id');
 const taskName = button.getAttribute('data-task-name');
 const taskDescription = button.getAttribute('data-task-description') || 'No description available';
 const taskStatus = button.getAttribute('data-task-status');

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
     if (file.path.match(/\.(jpeg|jpg|png|gif|svg)$/)) {
      return `
       <a href="${file.path}" target="_blank">
        <img src="${file.path}" alt="Reference Image" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
        <div class="preview-caption">${file.name}</div>
       </a>`;
     } else {
      return `<a href="${file.path}" target="_blank">${file.name}</a>`;
     }
    }).join('<br>');

    const attachmentsList = data.attachments.map(file => {
     if (file.path.match(/\.(jpeg|jpg|png|gif|svg)$/)) {
      return `
       <a href="${file.path}" target="_blank">
        <img src="${file.path}" alt="Attachment Image" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
        <div class="preview-caption">${file.name}</div>
       </a>`;
     } else {
      return `<a href="${file.path}" target="_blank">${file.name}</a>`;
     }
    }).join('<br>');

    Swal.fire({
     title: taskName,
     html: `
      <div class="text-start">
       <p><strong>Description:</strong><br>${formattedDescription}</p>
       <p><strong>Status:</strong> <span class="badge bg-${getStatusBadgeClass(taskStatus)}">${taskStatus}</span></p>
       <p><strong>References:</strong><br>${referencesList || 'No references available'}</p>
       <p><strong>Attachments:</strong><br>${attachmentsList || 'No attachments available'}</p>
      </div>
     `,
     icon: 'info',
     confirmButtonText: 'Close'
    });
   } else {
    throw new Error(data.message || 'Failed to fetch references and attachments');
   }
  })
  .catch(error => {
   Swal.fire({
    icon: 'error',
    title: 'Error',
    text: `Failed to load references and attachments: ${error.message}`,
   });
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