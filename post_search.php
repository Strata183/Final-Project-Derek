<?php
require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/helpers.php';

$keyword = trim($_GET['keyword'] ?? '');
$results = [];

if ($keyword !== '') {
    $like = '%' . $keyword . '%';
    $stmt = $conn->prepare("
        SELECT p.PostID, p.Title, p.Content, p.CreatedAt, u.Username
        FROM Posts p
        JOIN Users u ON u.UserID = p.UserID
        WHERE p.Title LIKE ? OR p.Content LIKE ?
        ORDER BY p.CreatedAt DESC
        LIMIT 50
    ");
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $results = stmt_fetch_all($stmt);
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Search Posts</title></head>
<body>
<h1>Search Posts</h1>
<p><a href="post_search.html">Back</a></p>

<?php if ($keyword === ''): ?>
  <p>Enter a keyword to search.</p>
<?php elseif (!$results): ?>
  <p>No results found.</p>
<?php else: ?>
  <ul>
    <?php foreach ($results as $row): ?>
      <li>
        <a href="post.php?PostID=<?= (int)$row['PostID'] ?>"><?= e($row['Title']) ?></a>
        by <?= e($row['Username']) ?>
        on <?= e($row['CreatedAt']) ?>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>
</body></html>
