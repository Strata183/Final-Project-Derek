<?php
require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/helpers.php';

require_login();

$stmt = $conn->prepare("SELECT CategoryID, CategoryName FROM Categories ORDER BY CategoryName ASC");
$stmt->execute();
$cats = stmt_fetch_all($stmt);
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>New Post</title><link rel="stylesheet" href="styles.css"></head>
<body>
<h1>New Post</h1>

<form method="post" action="insert_post.php">
  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

  <label>Title<br>
    <input name="Title" required maxlength="200">
  </label><br><br>

  <label>Category<br>
    <select name="CategoryID">
      <option value="0">(none)</option>
      <?php foreach ($cats as $c): ?>
        <option value="<?= (int)$c['CategoryID'] ?>"><?= e($c['CategoryName']) ?></option>
      <?php endforeach; ?>
    </select>
  </label><br><br>

  <label>Content<br>
    <textarea name="Content" rows="10" cols="70" required></textarea>
  </label><br><br>

  <button type="submit">Publish</button>
</form>

<p><a href="index.php">Back</a></p>
</body></html>
