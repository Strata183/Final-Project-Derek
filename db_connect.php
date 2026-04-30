<?php
$servername = "146.190.144.31";
$port = 33073;
$username = "student_s013";
$password = "5bIzdgSzmIV77J#KfMz-";
$dbname = "blog_s013";

// Create connection 
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>