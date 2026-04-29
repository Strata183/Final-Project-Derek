<?php
include 'db_connect.php';

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM Users WHERE Username='$username' AND Password='$password'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Login Successful
    echo "Login Successful";
    // Set session variables or redirect to another page
} else {
    // Login Failed
    echo "Invalid Username or Password";
}
$conn->close();
?>
