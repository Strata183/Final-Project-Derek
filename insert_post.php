<?php
require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';

require_login();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('new_post.php');
}

csrf_validate_post();

$Title = trim($_POST['Title'] ?? '');
$Content = trim($_POST['Content'] ?? '');
$CategoryID = (int)($_POST['CategoryID'] ?? 0);

if ($Title === '' || strlen($Title) > 200) $errors[] = "Title required (max 200).";
if ($Content === '') $errors[] = "Content required.";

if (!$errors) {
    $UserID = current_user_id();

    // If CategoryID=0 treat as NULL
    if ($CategoryID > 0) {
        $stmt = $conn->prepare("INSERT INTO Posts (UserID, CategoryID, Title, Content, CreatedAt) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("iiss", $UserID, $CategoryID, $Title, $Content);
    } else {
        $stmt = $conn->prepare("INSERT INTO Posts (UserID, CategoryID, Title, Content, CreatedAt) VALUES (?, NULL, ?, ?, NOW())");
        $stmt->bind_param("iss", $UserID, $Title, $Content);
    }

    $stmt->execute();
    redirect('index.php');
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>New Post - Error</title><link rel="stylesheet" href="styles.css"></head>
<body>
<h1>New Post</h1>
<ul><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul>
<p><a href="new_post.php">Go back</a></p>
</body></html>
