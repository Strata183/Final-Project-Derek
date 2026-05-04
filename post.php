<?php
require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';

$PostID = isset($_GET['PostID']) ? (int)$_GET['PostID'] : 0;

$stmt = $conn->prepare("
  SELECT p.PostID, p.Title, p.Content, p.CreatedAt,
         u.Username,
         c.CategoryName
  FROM Posts p
  JOIN Users u ON u.UserID = p.UserID
  LEFT JOIN Categories c ON c.CategoryID = p.CategoryID
  WHERE p.PostID = ?
");
$stmt->bind_param("i", $PostID);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();
if (!$post) { http_response_code(404); exit("Post not found"); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_login();
    csrf_validate_post();

    $Comment = trim($_POST['Comment'] ?? '');
    if ($Comment !== '') {
        $UserID = current_user_id();
        $stmt2 = $conn->prepare("INSERT INTO Comments (PostID, UserID, Comment, CreatedAt) VALUES (?, ?, ?, NOW())");
        $stmt2->bind_param("iis", $PostID, $UserID, $Comment);
        $stmt2->execute();
        redirect("post.php?PostID=" . $PostID);
    }
}

$stmt3 = $conn->prepare("
  SELECT c.CommentID, c.Comment, c.CreatedAt, u.Username
  FROM Comments c
  JOIN Users u ON u.UserID = c.UserID
  WHERE c.PostID = ?
  ORDER BY c.CreatedAt ASC
");
$stmt3->bind_param("i", $PostID);
$stmt3->execute();
$comments = $stmt3->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html><head><meta charset="utf-8"><title><?= e($post['Title']) ?></title></head>
<body>
<p><a href="index.php">← Back</a></p>

<h1><?= e($post['Title']) ?></h1>
<small>
  By <?= e($post['Username']) ?>
  in <?= e($post['CategoryName'] ?? 'Uncategorized') ?>
  on <?= e($post['CreatedAt']) ?>
</small>

<p><?= nl2br(e($post['Content'])) ?></p>

<hr>
<h2>Comments</h2>

<?php if (!$comments): ?>
  <p>No comments yet.</p>
<?php else: ?>
  <?php foreach ($comments as $c): ?>
    <div>
      <strong><?= e($c['Username']) ?></strong>
      <small>(<?= e($c['CreatedAt']) ?>)</small>
      <p><?= nl2br(e($c['Comment'])) ?></p>
    </div>
    <hr>
  <?php endforeach; ?>
<?php endif; ?>

<?php if (is_logged_in()): ?>
  <h3>Add a comment</h3>
  <form method="post" action="post.php?PostID=<?= (int)$PostID ?>">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <textarea name="Comment" rows="4" cols="60" required></textarea><br>
    <button type="submit">Post Comment</button>
  </form>
<?php else: ?>
  <p><a href="login.html">Login</a> to comment.</p>
<?php endif; ?>

</body></html>