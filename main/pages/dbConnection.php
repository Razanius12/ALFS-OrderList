<?php
$servername = "localhost"; // Ganti sesuai konfigurasi server Anda
$username = "root"; // Username database
$password = ""; // Password database (kosong jika default XAMPP)
$dbname = "your_database_name"; // Ganti dengan nama database Anda

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
