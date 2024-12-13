// Function to format input numbers with Rp prefix, thousand separators, and proper labels (Ribuan, Juta, Miliar)
function formatInput(input) {
 let value = input.value.replace(/[Rp\s.,]/g, ""); // Remove Rp, spaces, dots, commas
 value = parseInt(value, 10) || 0; // Parse value to integer, default to 0 if invalid

 // Formatting for large values with proper labels (Ribuan, Juta, Miliar)
 if (value >= 1_000_000_000) {
  input.value = `Rp ${value.toLocaleString("id-ID")} miliar`; // Display full value in miliar format
 } else if (value >= 1_000_000) {
  input.value = `Rp ${value.toLocaleString("id-ID")} juta`; // Display full value in juta format
 } else if (value >= 1_000) {
  input.value = `Rp ${value.toLocaleString("id-ID")} ribu`; // Display full value in ribuan format
 } else {
  input.value = "Rp " + value.toLocaleString("id-ID"); // Format with thousand separators for smaller values
 }
}

document.addEventListener("DOMContentLoaded", () => {
 const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
 const modal = document.getElementById("incomeModal");
 const openModalButton = document.getElementById("openModal");
 const closeModalButtons = [
  document.getElementById("closeModal"),
  document.getElementById("closeModalFooter")
 ];
 const saveIncomeButton = document.getElementById("saveIncome");
 const chartContext = document.getElementById("monthlyProgressBarChart").getContext("2d");

 // Load saved data from localStorage if available
 let savedData = JSON.parse(localStorage.getItem("monthlyIncome")) || new Array(12).fill(0);

 // Initialize chart data
 const chartData = {
  labels: months,
  datasets: [
   {
    label: "Penghasilan Per Bulan",
    backgroundColor: savedData.map((value) => {
     const maxValue = Math.max(...savedData); // Get the maximum value
     const thresholdHigh = maxValue * 0.75;  // 75% of max value
     const thresholdLow = maxValue * 0.50;   // 50% of max value

     // Determine color based on value range
     if (value >= thresholdHigh) {
      return "#4caf50";  // Green for high values
     } else if (value >= thresholdLow) {
      return "#ffeb3b";  // Yellow for medium values
     } else {
      return "#f44336";  // Red for low values
     }
    }),
    borderColor: "#388e3c",
    data: savedData
   }
  ]
 };

 const chartOptions = {
  responsive: true,
  maintainAspectRatio: false
 };

 const barChart = new Chart(chartContext, {
  type: "bar",
  data: chartData,
  options: chartOptions
 });

 // Function to update the modal inputs based on saved data
 function loadSavedData() {
  months.forEach((month, index) => {
   const input = document.getElementById(`income${month}`);
   const value = savedData[index];
   if (value >= 1_000_000_000) {
    input.value = `Rp ${value.toLocaleString("id-ID")} miliar`;
   } else if (value >= 1_000_000) {
    input.value = `Rp ${value.toLocaleString("id-ID")} juta`;
   } else {
    input.value = "Rp " + value.toLocaleString("id-ID");
   }
  });
 }

 loadSavedData(); // Load saved data into inputs when the page loads

 openModalButton.addEventListener("click", () => {
  modal.style.display = "block";
  // Make the modal bigger
  document.getElementById("modalDialog").classList.add("modal-lg");
 });

 closeModalButtons.forEach(button => {
  button.addEventListener("click", () => {
   modal.style.display = "none";
   // Reset the modal size when closed
   document.getElementById("modalDialog").classList.remove("modal-lg");
  });
 });

 saveIncomeButton.addEventListener("click", () => {
  months.forEach((month, index) => {
   const input = document.getElementById(`income${month}`);
   let value = input.value.replace(/[Rp\s.,]/g, ""); // Remove Rp, spaces, dots, commas
   value = parseFloat(value) || 0; // Convert to float, default to 0 if invalid
   savedData[index] = value; // Update only the specific index
  });

  // Save the updated data to localStorage
  localStorage.setItem("monthlyIncome", JSON.stringify(savedData));

  // Update chart data
  chartData.datasets[0].data = [...savedData]; // Spread the array to ensure the chart updates
  chartData.datasets[0].backgroundColor = savedData.map((value) => {
   const maxValue = Math.max(...savedData); // Get the maximum value
   const thresholdHigh = maxValue * 0.75;  // 75% of max value
   const thresholdLow = maxValue * 0.50;   // 50% of max value

   // Determine color based on value range
   if (value >= thresholdHigh) {
    return "#4caf50";  // Green for high values
   } else if (value >= thresholdLow) {
    return "#ffeb3b";  // Yellow for medium values
   } else {
    return "#f44336";  // Red for low values
   }
  });
  barChart.update();

  modal.style.display = "none";
  // Reset the modal size when closed
  document.getElementById("modalDialog").classList.remove("modal-lg");
 });
});