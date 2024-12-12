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

    const chartData = {
        labels: months,
        datasets: [
            {
                label: "Monthly Income",
                backgroundColor: "#4caf50",
                borderColor: "#388e3c",
                data: new Array(12).fill(0)
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

    // Open modal
    openModalButton.addEventListener("click", () => {
        modal.style.display = "block";
    });

    // Close modal
    closeModalButtons.forEach(button => {
        button.addEventListener("click", () => {
            modal.style.display = "none";
        });
    });

    // Format number with thousands separator
    const formatNumber = (value) => {
        const number = value.replace(/\D/g, ""); // Remove non-numeric characters
        return number.replace(/\B(?=(\d{3})+(?!\d))/g, "."); // Add dots as thousands separators
    };

    // Add input event listener to format number
    months.forEach(month => {
        const input = document.getElementById(`income${month}`);
        input.addEventListener("input", () => {
            input.value = formatNumber(input.value);
        });
    });

    // Save income and update chart
    saveIncomeButton.addEventListener("click", () => {
        months.forEach((month, index) => {
            const input = document.getElementById(`income${month}`);
            const value = parseFloat(input.value.replace(/\./g, "")) || 0; // Remove dots before parsing
            chartData.datasets[0].data[index] = value;
        });
        barChart.update();
        modal.style.display = "none";
    });
);
