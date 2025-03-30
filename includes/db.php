<?php
// Database configuration
$servername = "localhost";  // Default for XAMPP
$username = "root";         // Default XAMPP username
$password = "";             // Default XAMPP password (empty)
$database = "appliance_repair_db"; // Change this to your actual database name

// Create a new MySQLi connection
$conn = new mysqli($servername, $username, $password, $database);

// Check if the connection is successful
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Set character encoding to UTF-8
$conn->set_charset("utf8");

// Optional: Uncomment this line to confirm the connection (for debugging only)
// echo "Database connected successfully!";
?>
