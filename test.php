<?php
// Include database connection
include 'includes/db.php';

// Check if the connection is successful
if ($conn) {
    echo "<h2 style='color: green;'> Database connection successful!</h2>";
} else {
    die("<h2 style='color: red;'> Database connection failed: " . mysqli_connect_error() . "</h2>");
}

// Test running a query
$sql = "SELECT 1";
if (mysqli_query($conn, $sql)) {
    echo "<p style='color: green;'> Test query executed successfully!</p>";
} else {
    echo "<p style='color: red;'> Error executing test query: " . mysqli_error($conn) . "</p>";
}

// Close the connection
mysqli_close($conn);
?>
