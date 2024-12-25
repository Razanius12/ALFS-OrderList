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

 Swal.fire({
  title: taskName,
  html: `
      <div class="text-start">
        <p><strong>Description:</strong><br>${formattedDescription}</p>
        <p><strong>Status:</strong> <span class="badge bg-${getStatusBadgeClass(taskStatus)}">${taskStatus}</span></p>
      </div>
    `,
  icon: 'info',
  confirmButtonText: 'Close'
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