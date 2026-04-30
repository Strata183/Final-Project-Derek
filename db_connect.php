<?php

$DB_HOST = "localhost";
$DB_USER = "your_user";
$DB_PASS = "your_password_here";
$DB_NAME = "blog_db";

// Create connection 
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?