<?php
$host = "localhost";
$username = "root";  // Change this to your database username
$password = "";      // Change this to your database password
$database = "db_alf_solution";

// Create connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
 die("Connection failed: " . mysqli_connect_error());
}