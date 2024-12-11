<?php
require 'main/common/allowedRoles.php';
try {
    sharedAccessPage();

    // Get current user details
    $currentUser = getCurrentUserDetails();
?>

<div class="container">
    <div class="page-inner">
        <div class="page-header mb-0">
            <h3 class="fw-bold mb-3">Daily Progress</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                    <a href="./index.php">
                        <i class="icon-home"></i>
                    </a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                    <a href="./index.php?page=alfOffices">Daily Progress</a>
                </li>
            </ul>
        </div>
        <div class="page-category">
            Simple yet flexible JavaScript charting for designers & developers. Please checkout their
            <a href="http://www.chartjs.org/" target="_blank">full documentation</a>.
            <br><br>
            wip, all placeholder
        </div>
        <div class="row">

            <!-- Daily Client Requests -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Daily Client Requests</div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap mb-3">
                            <?php 
                            $months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                            foreach ($months as $month): ?>
                                <div class="me-3">
                                    <label for="price<?= $month ?>" class="form-label"><?= $month ?>:</label>
                                    <input type="text" id="price<?= $month ?>" class="form-control" style="width: 100px;" placeholder="Price">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="chart-container">
                            <canvas id="dailyProgressBarChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Sales -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Monthly Sales</div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="monthlyProgressBarChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const prices = {};
    const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

    months.forEach(month => {
        prices[month] = document.getElementById(`price${month}`);
    });

    const formatNumber = (num) => {
        return num.toLocaleString('id-ID');
    };

    const unformatNumber = (str) => {
        return parseFloat(str.replace(/\./g, ''));
    };

    // Initialize input values with formatted numbers
    months.forEach(month => {
        const input = prices[month];
        input.addEventListener("input", () => {
            let rawValue = input.value.replace(/\./g, '');
            if (!isNaN(rawValue) && rawValue !== "") {
                input.value = formatNumber(parseFloat(rawValue));
            }
        });

        input.addEventListener("blur", () => {
            let rawValue = input.value.replace(/\./g, '');
            if (!isNaN(rawValue) && rawValue !== "") {
                input.value = formatNumber(parseFloat(rawValue));
            }
        });
    });

    const dailyChart = document.getElementById("dailyProgressBarChart").getContext("2d");

    const chartData = {
        labels: months,
        datasets: [{
            label: "Monthly Profit",
            backgroundColor: "#4caf50",
            borderColor: "#388e3c",
            data: new Array(12).fill(0) // Initial data for each month
        }]
    };

    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false
    };

    const barChart = new Chart(dailyChart, {
        type: "bar",
        data: chartData,
        options: chartOptions
    });

    months.forEach((month, index) => {
        prices[month].addEventListener("change", () => {
            const value = unformatNumber(prices[month].value);
            if (!isNaN(value)) {
                chartData.datasets[0].data[index] = value; // Update data for the respective month
                barChart.update();

                // Format the input field with dots
                prices[month].value = formatNumber(value);
            }
        });
    });
});
</script>

<?php
} catch (Exception $e) {
    // Handle any unexpected errors
}
?>
