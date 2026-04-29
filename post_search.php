<?php
include 'db_connect.php';

function searchPosts($keyword) {
    global $conn;
    
    // Directly inserting the keyword into the SQL query
    $sql = "SELECT * FROM BlogPosts WHERE Title LIKE '%$keyword%' OR Content LIKE '%$keyword%'";

    // Execute the query
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch and display all posts containing the keyword
        while($row = $result->fetch_assoc()) {
            echo "PostID: " . $row["PostID"]. " - Title: " . $row["Title"]. " - Content: " . $row["Content"]. "<br>";
        }
    } else {
        echo "No results found";
    }
}

// Example usage
searchPosts("example_keyword");

$conn->close();
?>
