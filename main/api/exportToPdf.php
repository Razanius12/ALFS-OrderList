<?php
require '../fpdf/fpdf.php';
require '../../config/database.php';

class PDF extends FPDF
{
 // Page header
 function Header()
 {
  $this->SetFont('Times', 'B', 14);
  $this->Cell(0, 10, 'ALF Solution OrderList - Exported Data', 0, 1, 'C');
  $this->Ln(10);
 }

 // Page footer
 function Footer()
 {
  $this->SetY(-15);
  $this->SetFont('Times', 'I', 8);
  $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
 }

 // Table with data
 function Table($header, $data)
 {
  // Add "No." column to the header
  array_unshift($header, 'No. ');

  // Calculate column widths
  $colWidths = [];
  foreach ($header as $col) {
   $colWidths[] = $this->GetStringWidth($col) + 1; // Minimum width for headers
  }
  foreach ($data as $index => $row) {
   foreach (array_merge([strval($index + 1)], array_values($row)) as $key => $col) {
    $colWidths[$key] = max($colWidths[$key], $this->GetStringWidth($col));
   }
  }

  // Draw header row
  $this->SetFont('Times', 'B', 12);
  foreach ($header as $key => $col) {
   $this->Cell($colWidths[$key], 7, $col, 1);
  }
  $this->Ln();

  // Draw data rows
  $this->SetFont('Times', '', 12);
  foreach ($data as $index => $row) {
   $this->Cell($colWidths[0], 7, $index + 1, 1); // Add row number
   foreach (array_values($row) as $key => $col) {
    $this->Cell($colWidths[$key + 1], 7, $col, 1);
   }
   $this->Ln();
  }
 }

}

$pdf = new PDF();
$pdf->AddPage();

function fetchDataWithRelations($conn, $table)
{
 $query = "";
 $headers = [];
 $data = [];

 // Handle specific cases for relationships
 switch ($table) {
  case 'admins':
   $query = "SELECT a.name_admin, p.position_name AS position, a.phone_number
                      FROM admins a 
                      LEFT JOIN positions p ON a.id_position = p.id_position";
   $headers = ['Name', 'Position', 'Phone Number'];
   break;

  case 'orders':
   $query = "SELECT o.order_name, o.status, 
                             CONCAT('$ ', o.order_price) AS order_price, 
                             w.name_worker AS worker
                      FROM orders o
                      LEFT JOIN workers w ON o.worker_id = w.id_worker";
   $headers = ['Order Name', 'Status', 'Price', 'Assigned Worker'];
   break;

  case 'positions':
   $query = "SELECT position_name, department, created_at FROM positions";
   $headers = ['Position Name', 'Department', 'Created At'];
   break;

  case 'workers':
   $query = "SELECT w.name_worker, p.position_name AS position, w.gender_worker, w.phone_number
                      FROM workers w
                      LEFT JOIN positions p ON w.id_position = p.id_position";
   $headers = ['Name', 'Position', 'Gender', 'Phone Number'];
   break;
 }

 if ($query) {
  $result = mysqli_query($conn, $query);
  while ($row = mysqli_fetch_assoc($result)) {
   $data[] = $row;
  }
 }

 return [$headers, $data];
}

// Generate the PDF
$pdf = new PDF();
$pdf->AddPage();

$tables = ['admins', 'orders', 'positions', 'workers'];

foreach ($tables as $table) {
 $pdf->SetFont('Times', 'B', 14);
 $pdf->Cell(0, 10, ucfirst($table), 0, 1, 'L');
 $pdf->Ln(5);

 list($header, $data) = fetchDataWithRelations($conn, $table);
 if ($data) {
  $pdf->Table($header, $data);
  $pdf->Ln(10);
 } else {
  $pdf->SetFont('Times', 'I', 12);
  $pdf->Cell(0, 10, 'No data available.', 0, 1, 'L');
  $pdf->Ln(10);
 }
}

// Monthly Recap Statistics
$selectedMonth = date('m');
$selectedYear = date('Y');

// Fetch all orders for the selected month and year
$ordersQuery = "SELECT * FROM orders WHERE status = 'COMPLETED' AND MONTH(finished_at) = ? AND YEAR(finished_at) = ?";
$ordersStmt = mysqli_prepare($conn, $ordersQuery);
mysqli_stmt_bind_param($ordersStmt, "ii", $selectedMonth, $selectedYear);
mysqli_stmt_execute($ordersStmt);
$ordersResult = mysqli_stmt_get_result($ordersStmt);

$dailyStatistics = [];
$workerPerformance = [];

while ($order = mysqli_fetch_assoc($ordersResult)) {
 $date = date('Y-m-d', strtotime($order['finished_at']));
 $workerId = $order['worker_id'];
 $orderPrice = $order['order_price'];

 // Daily statistics
 if (!isset($dailyStatistics[$date])) {
  $dailyStatistics[$date] = [
   'total_income' => 0,
   'order_count' => 0,
   'order_prices' => []
  ];
 }
 $dailyStatistics[$date]['total_income'] += $orderPrice;
 $dailyStatistics[$date]['order_count'] += 1;
 $dailyStatistics[$date]['order_prices'][] = $orderPrice;

 // Worker performance
 if (!isset($workerPerformance[$workerId])) {
  $workerPerformance[$workerId] = [
   'name_worker' => '',
   'completed_orders' => 0,
   'total_earnings' => 0
  ];
 }
 $workerPerformance[$workerId]['completed_orders'] += 1;
 $workerPerformance[$workerId]['total_earnings'] += $orderPrice;
}

// Fetch worker names
$workersQuery = "SELECT id_worker, name_worker FROM workers";
$workersResult = mysqli_query($conn, $workersQuery);
while ($worker = mysqli_fetch_assoc($workersResult)) {
 if (isset($workerPerformance[$worker['id_worker']])) {
  $workerPerformance[$worker['id_worker']]['name_worker'] = $worker['name_worker'];
 }
}

// Add Monthly Recap section to PDF
$pdf->SetFont('Times', 'B', 14);
$pdf->Cell(0, 10, 'Monthly Recap - ' . date('F Y', mktime(0, 0, 0, $selectedMonth, 1, $selectedYear)), 0, 1, 'L');
$pdf->Ln(5);

// Daily Statistics Table
$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(0, 10, 'Daily Statistics', 0, 1, 'L');
$pdf->SetFont('Times', '', 12);

// Headers for Daily Statistics Table
$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(10, 7, 'No.', 1, 0, 'C',);
$pdf->Cell(20, 7, 'Date', 1, 0);
$pdf->Cell(30, 7, 'Total Income', 1, 0);
$pdf->Cell(26, 7, 'Order Count', 1, 0);
$pdf->Cell(40, 7, 'Avg Order Value', 1, 0);
$pdf->Cell(30, 7, 'Highest Order', 1, 0);
$pdf->Cell(30, 7, 'Lowest Order', 1, 1);
$pdf->SetFont('Times', '', 12);

$dailyQuery = "SELECT 
    DATE(finished_at) AS order_date, 
    SUM(order_price) AS total_income,
    COUNT(*) AS order_count,
    ROUND(AVG(order_price), 2) AS average_order_value,
    MAX(order_price) AS highest_order,
    MIN(order_price) AS lowest_order
  FROM orders 
  WHERE status = 'COMPLETED' 
    AND MONTH(finished_at) = ? 
    AND YEAR(finished_at) = ?
  GROUP BY DATE(finished_at) 
  ORDER BY order_date";
$dailyStmt = mysqli_prepare($conn, $dailyQuery);
mysqli_stmt_bind_param($dailyStmt, "ii", $selectedMonth, $selectedYear);
mysqli_stmt_execute($dailyStmt);
$dailyResult = mysqli_stmt_get_result($dailyStmt);

$no = 1;
while ($daily = mysqli_fetch_array($dailyResult)) {
 $pdf->Cell(10, 7, $no++, 1, 0);
 $pdf->Cell(20, 7, date('d M', strtotime($daily['order_date'])), 1, 0);
 $pdf->Cell(30, 7, '$ ' . $daily['total_income'], 1, 0);
 $pdf->Cell(26, 7, $daily['order_count'], 1, 0);
 $pdf->Cell(40, 7, '$ ' . number_format($daily['average_order_value'], 2), 1, 0);
 $pdf->Cell(30, 7, '$ ' . $daily['highest_order'], 1, 0);
 $pdf->Cell(30, 7, '$ ' . $daily['lowest_order'],  1, 1);
}

$pdf->Ln(30);

// Worker Performance Table
$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(0, 10, 'Worker Performance', 0, 1, 'L');
$pdf->SetFont('Times', '', 12);

// Headers for Worker Performance Table
$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(10, 7, 'No.', 1, 0);
$pdf->Cell(50, 7, 'Worker Name', 1, 0);
$pdf->Cell(50, 7, 'Completed Orders', 1, 0);
$pdf->Cell(50, 7, 'Total Earnings', 1, 1);
$pdf->SetFont('Times', '', 12);

$workerQuery = "SELECT 
    w.name_worker,
    COUNT(CASE WHEN o.status = 'COMPLETED' THEN 1 END) as completed_orders,
    COALESCE(SUM(CASE WHEN o.status = 'COMPLETED' THEN o.order_price ELSE 0 END), 0) as total_earnings
  FROM workers w
  LEFT JOIN orders o ON w.id_worker = o.worker_id
    AND MONTH(o.finished_at) = ? 
    AND YEAR(o.finished_at) = ?
  GROUP BY w.id_worker, w.name_worker
  ORDER BY total_earnings DESC";
$workerStmt = mysqli_prepare($conn, $workerQuery);
mysqli_stmt_bind_param($workerStmt, "ii", $selectedMonth, $selectedYear);
mysqli_stmt_execute($workerStmt);
$workerResult = mysqli_stmt_get_result($workerStmt);

$no = 1;
while ($worker = mysqli_fetch_array($workerResult)) {
 $pdf->Cell(10, 7, $no++, 1, 0, 'C');
 $pdf->Cell(50, 7, $worker['name_worker'], 1, 0);
 $pdf->Cell(50, 7, $worker['completed_orders'], 1, 0);
 $pdf->Cell(50, 7, '$ ' . $worker['total_earnings'], 1, 1);
}

$pdf->Output();
?>