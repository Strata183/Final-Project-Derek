<?php
require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/helpers.php';

$sql = "
    SELECT p.PostID, p.Title, p.Content, p.CreatedAt, u.Username
    FROM Posts p
    JOIN Users u ON u.UserID = p.UserID
    ORDER BY p.CreatedAt DESC
";
$result = $conn->query($sql);
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>All Posts</title></head>
<body>
<h1>All Posts</h1>
<p><a href="index.php">Back</a></p>

<?php if ($result->num_rows === 0): ?>
  <p>No posts yet.</p>
<?php else: ?>
  <ul>
    <?php while ($row = $result->fetch_assoc()): ?>
      <li>
        <a href="post.php?PostID=<?= (int)$row['PostID'] ?>"><?= e($row['Title']) ?></a>
        by <?= e($row['Username']) ?>
        on <?= e($row['CreatedAt']) ?>
      </li>
    <?php endwhile; ?>
  </ul>
<?php endif; ?>
</body></html>
