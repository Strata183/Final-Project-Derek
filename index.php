<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';

$posts = [];
$loadError = '';

try {
    $stmt = $conn->prepare("
      SELECT p.PostID, p.Title, p.Content, p.CreatedAt,
             u.Username,
             c.CategoryName
      FROM Posts p
      JOIN Users u ON u.UserID = p.UserID
      LEFT JOIN Categories c ON c.CategoryID = p.CategoryID
      ORDER BY p.CreatedAt DESC
      LIMIT 50
    ");
    $stmt->execute();
    $posts = stmt_fetch_all($stmt);
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    $loadError = 'Could not load posts: ' . $e->getMessage();
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Blog</title></head>
<body>
<h1>Blog</h1>

<nav>
  <a href="index.php">Home</a> |
  <a href="search.html">Search</a> |
  <?php if (is_logged_in()): ?>
    Logged in as <?= e(current_username()) ?> |
    <a href="new_post.php">New Post</a> |
    <a href="logout.php">Logout</a>
  <?php else: ?>
    <a href="login.php">Login</a> |
    <a href="register.php">Register</a>
  <?php endif; ?>
</nav>

<hr>

<?php if ($loadError): ?>
  <p><?= e($loadError) ?></p>
  <p>Make sure the database credentials in config.php are correct and index.sql has been run.</p>
<?php elseif (!$posts): ?>
  <p>No posts yet.</p>
<?php endif; ?>

<?php foreach ($posts as $p): ?>
  <article>
    <h2><a href="post.php?PostID=<?= (int)$p['PostID'] ?>"><?= e($p['Title']) ?></a></h2>
    <small>
      By <?= e($p['Username']) ?>
      in <?= e($p['CategoryName'] ?? 'Uncategorized') ?>
      on <?= e($p['CreatedAt']) ?>
    </small>
    <p><?= nl2br(e(excerpt($p['Content']))) ?></p>
  </article>
  <hr>
<?php endforeach; ?>

</body></html>
