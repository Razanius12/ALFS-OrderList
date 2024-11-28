document.addEventListener('DOMContentLoaded', function () {

 $(document).ready(function () {

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
      data: [3, 2, 9, 5, 4, 6, 4, 6, 7, 8, 7, 4],
     },
    ],
   },
   options: {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
     y: {
      beginAtZero: true,
      min: 0, // Set minimum value for y-axis
      max: 10, // Set maximum value for y-axis
      ticks: {
       stepSize: 1, // Set the interval between ticks
       display: true // Ensure ticks are displayed
      }
     }
    },
   },
  });

 });

});