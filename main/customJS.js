// Get the canvas element
const ctx = document.getElementById('dailyProgressBarChart').getContext('2d');

// Create the chart instance
const dpbc = new Chart(ctx, {
 type: 'bar', // or any other chart type
 data: {
  labels: [
   "Jan",
   "Feb",
   "Mar",
   "Apr",
   "May",
   "Jun",
   "Jul",
   "Aug",
   "Sep",
   "Oct",
   "Nov",
   "Dec",
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
   yAxes: [
    {
     ticks: {
      beginAtZero: true,
     },
    },
   ],
  },
 },
});