<?php
include 'db_connect.php';

$title = $_POST['title'];
$content = $_POST['content'];
$userid = $_POST['userid']; // Assuming user id is known
$categoryid = $_POST['categoryid']; // Assuming category id is known

$sql = "INSERT INTO BlogPosts (UserID, Title, Content, CategoryID)
VALUES ('$userid', '$title', '$content', '$categoryid')";

if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
