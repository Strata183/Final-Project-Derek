<?php
// Database configuration
$servername = "146.190.144.31:33073"; // IP address and custom port
$username   = "student_s013";         // Your specific student user
$password   = "5bIzdgSzmIV77J#KfMz-"; // Your specific password
$dbname     = "blog_s013";           // Your specific database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // During development, showing the error is helpful. 
    // In production, you'd want to log this instead of displaying it.
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Set charset to utf8mb4 for better character support
$conn->set_charset("utf8mb4");

echo "Connected successfully to " . $dbname;
?>
