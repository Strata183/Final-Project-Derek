<?php
require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';

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
$posts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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
    <a href="new_post.html">New Post</a> |
    <a href="logout.php">Logout</a>
  <?php else: ?>
    <a href="login.html">Login</a> |
    <a href="register.html">Register</a>
  <?php endif; ?>
</nav>

<hr>

<?php foreach ($posts as $p): ?>
  <article>
    <h2><a href="post.php?PostID=<?= (int)$p['PostID'] ?>"><?= e($p['Title']) ?></a></h2>
    <small>
      By <?= e($p['Username']) ?>
      in <?= e($p['CategoryName'] ?? 'Uncategorized') ?>
      on <?= e($p['CreatedAt']) ?>
    </small>
    <p><?= nl2br(e(mb_strimwidth($p['Content'], 0, 300, '...'))) ?></p>
  </article>
  <hr>
<?php endforeach; ?>

</body></html>