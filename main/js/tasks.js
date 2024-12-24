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
  title: 'Mark Task as Complete?',
  text: "Are you sure you want to mark this task as completed?",
  icon: 'warning',
  showCancelButton: true,
  confirmButtonColor: '#3085d6',
  cancelButtonColor: '#d33',
  confirmButtonText: 'Yes, complete it!'
 }).then((result) => {
  if (result.isConfirmed) {
   fetch('main/api/updateTask.php', {
    method: 'POST',
    headers: {
     'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: new URLSearchParams({
     task_id: taskId,
     status: 'COMPLETED'
    })
   })
    .then(response => {
     // Check if the response is OK (status in 200-299 range)
     if (!response.ok) {
      // If not OK, try to parse error message from response
      return response.json().then(errorData => {
       throw new Error(errorData.message || 'Server error');
      });
     }
     return response.json();
    })
    .then(data => {
     if (data.success) {
      Swal.fire({
       title: 'Task Completed!',
       text: 'The task has been successfully marked as completed. You will not see the task again when it\'s completed.',
       icon: 'success',
       confirmButtonText: 'OK'
      }).then(() => {
       location.reload();
      });
     } else {
      throw new Error(data.message || 'Failed to update task status');
     }
    })
    .catch(error => {
     console.error('Error:', error);
     Swal.fire({
      title: 'Error',
      text: error.message || 'An error occurred while updating the task status.',
      icon: 'error',
      confirmButtonText: 'OK'
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