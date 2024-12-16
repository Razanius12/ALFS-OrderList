<?php

// Halaman hanya untuk admin
adminOnlyPage();

// Ambil data pengguna saat ini
$currentUser = getCurrentUserDetails();

// Periksa akses halaman
checkPageAccess();

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
    </div>

    <!-- Tombol untuk membuka modal -->
    <button class="btn btn-primary" id="openModal">Penghasilan Per Hari</button>

    <!-- Modal -->
    <div class="modal" id="incomeModal" style="display: none;">
      <div class="modal-dialog" id="modalDialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Penghasilan Per Hari</h5>
            <button type="button" class="btn-close" id="closeModal"></button>
          </div>
          <div class="modal-body">
            <div class="d-flex flex-wrap mb-3">
              <?php
              $days = ["Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
              foreach ($days as $day): ?>
                <div class="me-3 mb-2">
                  <label for="income<?= $day ?>" class="form-label"><?= $day ?>:</label>
                  <input type="text" id="income<?= $day ?>" class="form-control" style="width: 150px;" placeholder="Penghasilan"
                    oninput="formatInput(this)">
                  <br>
                  <label for="date<?= $day ?>" class="form-label">Tanggal:</label>
                  <input type="date" id="date<?= $day ?>" class="form-control" style="width: 150px;">
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-success" id="saveIncome">Save</button>
            <button type="button" class="btn btn-danger" id="clearIncome">Clear</button>
            <button type="button" class="btn btn-secondary" id="closeModalFooter">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Bagian Grafik -->
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <div class="card-title">Penghasilan Per Hari</div>
          </div>
          <div class="card-body">
            <div class="chart-container">
              <canvas id="dailyProgressBarChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Fungsi Modal
const openModal = document.getElementById('openModal');
const closeModal = document.getElementById('closeModal');
const closeModalFooter = document.getElementById('closeModalFooter');
const incomeModal = document.getElementById('incomeModal');

openModal.addEventListener('click', () => {
  incomeModal.style.display = 'block';
});

closeModal.addEventListener('click', () => {
  incomeModal.style.display = 'none';
});

closeModalFooter.addEventListener('click', () => {
  incomeModal.style.display = 'none';
});

// Fungsi untuk menambahkan label Ribu, Juta, Miliar langsung saat input
function formatInput(input) {
  let value = input.value.replace(/[^0-9]/g, ''); // Hapus karakter selain angka

  if (value) {
    let formattedValue = new Intl.NumberFormat('id-ID').format(value); // Format angka
    input.value = formattedValue;
  } else {
    input.value = ''; // Reset jika kosong
  }
}

// Inisialisasi Chart.js
const ctx = document.getElementById('dailyProgressBarChart').getContext('2d');
const dailyProgressBarChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ["Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"],
    datasets: [{
      label: 'Penghasilan',
      data: [0, 0, 0, 0, 0, 0], // Data default
      backgroundColor: 'rgba(54, 162, 235, 0.2)',
      borderColor: 'rgba(54, 162, 235, 1)',
      borderWidth: 1
    }]
  },
  options: {
    responsive: true,
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          callback: function(value) {
            // Menambahkan label ribuan, jutaan, miliaran
            if (value >= 1000000000) {
              return (value / 1000000000).toFixed(1) + ' Miliar';
            } else if (value >= 1000000) {
              return (value / 1000000).toFixed(1) + ' Juta';
            } else if (value >= 1000) {
              return (value / 1000).toFixed(1) + ' Ribu';
            }
            return value;
          }
        }
      }
    },
    plugins: {
      tooltip: {
        callbacks: {
          label: function(tooltipItem) {
            const dayIndex = tooltipItem.dataIndex;
            const day = days[dayIndex]; // Menentukan nama hari
            const date = dates[dayIndex]; // Mengambil tanggal yang sesuai
            return `${day}: ${new Intl.NumberFormat('id-ID').format(tooltipItem.raw)} - Tanggal: ${date}`;
          }
        }
      }
    }
  }
});

// Simpan Data Penghasilan dan Tanggal
const saveIncome = document.getElementById('saveIncome');
saveIncome.addEventListener('click', () => {
  const data = [];
  const dates = [];
  const days = ["Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];

  days.forEach(day => {
    const incomeInput = document.getElementById(`income${day}`);
    const dateInput = document.getElementById(`date${day}`);
    
    // Ambil nilai penghasilan dan hapus pemisah ribuan
    const incomeValue = parseFloat(incomeInput.value.replace(/\./g, '')) || 0;
    const dateValue = dateInput.value || '';

    data.push(incomeValue);
    dates.push(dateValue);
  });

  // Simpan data ke localStorage
  const dailyData = { incomes: data, dates: dates };
  localStorage.setItem('dailyProgressData', JSON.stringify(dailyData));

  // Update grafik dengan data baru
  dailyProgressBarChart.data.labels = dates; // Menambahkan tanggal ke labels
  dailyProgressBarChart.data.datasets[0].data = data; // Memperbarui data penghasilan
  dailyProgressBarChart.update();

  incomeModal.style.display = 'none';
});

// Tombol Hapus Data Penghasilan
const clearIncome = document.getElementById('clearIncome');
clearIncome.addEventListener('click', () => {
  // Reset semua input ke kosong
  const days = ["Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
  days.forEach(day => {
    const incomeInput = document.getElementById(`income${day}`);
    const dateInput = document.getElementById(`date${day}`);
    incomeInput.value = '';
    dateInput.value = '';
  });

  // Hapus data dari localStorage
  localStorage.removeItem('dailyProgressData');

  // Reset grafik ke data awal
  dailyProgressBarChart.data.labels = ["Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
  dailyProgressBarChart.data.datasets[0].data = [0, 0, 0, 0, 0, 0];
  dailyProgressBarChart.update();
});

// Ambil data dari localStorage saat halaman dimuat
window.addEventListener('load', () => {
  const storedData = localStorage.getItem('dailyProgressData');
  if (storedData) {
    const data = JSON.parse(storedData);
    const incomes = data.incomes;
    const dates = data.dates;

    // Update grafik dengan data yang disimpan di localStorage
    dailyProgressBarChart.data.labels = dates; // Memperbarui label dengan tanggal
    dailyProgressBarChart.data.datasets[0].data = incomes;
    dailyProgressBarChart.update();

    // Isi input dengan data yang ada di localStorage
    const days = ["Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
    days.forEach((day, index) => {
      const incomeInput = document.getElementById(`income${day}`);
      const dateInput = document.getElementById(`date${day}`);
      
      incomeInput.value = new Intl.NumberFormat('id-ID').format(incomes[index]);
      dateInput.value = dates[index];
    });
  }
});
</script>
