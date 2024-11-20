// Function to populate edit modal
document.addEventListener('DOMContentLoaded', function () {
 // Ensure modal trigger buttons are correctly set up
 const editButtons = document.querySelectorAll('.edit-position-btn');

 editButtons.forEach(button => {
  button.addEventListener('click', function () {
   // Get position details from data attributes
   const positionId = this.getAttribute('data-position-id');
   const positionName = this.getAttribute('data-position-name');
   const department = this.getAttribute('data-department');

   // Populate modal fields
   document.getElementById('editPositionId').value = positionId;
   document.getElementById('editPositionName').value = positionName;
   document.getElementById('editPositionDepartment').value = department;

   // Show the modal
   $('#editPositionModal').modal('show');
  });
 });

 // Handle form submission
 document.getElementById('editPositionForm').addEventListener('submit', function (e) {
  e.preventDefault();

  const formData = new FormData(this);

  fetch('updatePosition.php', {
   method: 'POST',
   body: formData
  })
   .then(response => response.json())
   .then(data => {
    if (data.success) {
     // Update table row
     const positionId = formData.get('id_position');
     const row = document.querySelector(`.edit-position-btn[data-position-id="${positionId}"]`).closest('tr');

     row.cells[1].textContent = formData.get('position_name');
     row.cells[2].textContent = formData.get('department');

     // Close modal
     $('#editPositionModal').modal('hide');

     // Show success message
     alert(data.message || 'Position updated successfully');
    } else {
     // Show error in modal
     const errorContainer = document.getElementById('editPositionErrorContainer');
     errorContainer.innerHTML = data.message || 'Error updating position';
     errorContainer.style.display = 'block';
    }
   })
   .catch(error => {
    console.error('Error:', error);
    const errorContainer = document.getElementById('editPositionErrorContainer');
    errorContainer.innerHTML = 'An unexpected error occurred';
    errorContainer.style.display = 'block';
   });
 });
});

function deletePosition(positionId) {
 if (confirm('Are you sure you want to delete this position?')) {
  const deleteUrl = 'main/api/deletePosition.php?id=' + encodeURIComponent(positionId);

  fetch(deleteUrl, {
   method: 'GET',
   headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json'
   }
  })
   .then(response => {
    // Log entire response for debugging
    console.log('Full Response:', response);

    // Check content type
    const contentType = response.headers.get('content-type');
    console.log('Content Type:', contentType);

    // If response is not JSON, try to get text and log it
    if (!contentType || !contentType.includes('application/json')) {
     return response.text().then(text => {
      console.error('Non-JSON Response:', text);
      throw new Error('Expected JSON, got: ' + text);
     });
    }

    // If it is JSON, parse it
    return response.json();
   })
   .then(data => {
    console.log('Parsed Response:', data);

    if (data.success) {
     // Remove the table row
     const row = document.querySelector(`button[data-position-id="${positionId}"]`).closest('tr');
     if (row) {
      row.remove();
     }
     alert(data.message || 'Position deleted successfully');
    } else {
     alert(data.message || 'Error deleting position');
    }
   })
   .catch(error => {
    console.error('Full Delete Error:', {
     message: error.message,
     name: error.name,
     stack: error.stack
    });
    alert('Error deleting position: ' + error.message);
   });
 }
}

// Optional: Add edit functionality
function editPosition(positionId) {
 // Implement edit modal or inline editing logic
 console.log('Edit position:', positionId);
}