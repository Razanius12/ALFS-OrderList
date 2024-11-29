document.addEventListener('DOMContentLoaded', function () {

 // Destroy existing chart if it exists
 if (window.dailyProgressBarChart instanceof Chart) {
  window.dailyProgressBarChart.destroy();
 }

 // Get the canvas element
 const ctx = document.getElementById('dailyProgressBarChart').getContext('2d');

 // Create the chart instance
 window.dailyProgressBarChart = new Chart(ctx, {
  type: 'bar', // or any other chart type
  data: {
   labels: [
    "Jan", "Feb", "Mar", "Apr", "May", "Jun",
    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
   ],
   datasets: [
    {
     label: "Clients",
     backgroundColor: "rgb(23, 125, 255)",
     borderColor: "rgb(23, 125, 255)",
     data: [1, 2, 5, 6, 5, 4, 6, 4, 6, 7, 8, 7, 0],
    },
   ],
  },
  options: {
   responsive: true,
   maintainAspectRatio: false,
   scales: {
    y: {
     beginAtZero: true, // Pastikan sumbu y mulai dari 0
     min: 0, // Set nilai minimum untuk sumbu y ke 0
     ticks: {
      callback: function (value) {
       return value; // Menampilkan nilai tick
      }
     }
    }
   }
  },
 });
});