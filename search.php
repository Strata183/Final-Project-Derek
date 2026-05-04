<?php
require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/helpers.php';

$q = trim($_GET['q'] ?? '');
$CategoryID = (int)($_GET['CategoryID'] ?? 0);
$results = [];

if ($q !== '' || $CategoryID > 0) {
    // LIKE-based search for maximum compatibility.
    $like = '%' . $q . '%';

    if ($CategoryID > 0 && $q !== '') {
        $stmt = $conn->prepare("
          SELECT p.PostID, p.Title, p.CreatedAt, u.Username
          FROM Posts p
          JOIN Users u ON u.UserID = p.UserID
          WHERE p.CategoryID = ? AND (p.Title LIKE ? OR p.Content LIKE ?)
          ORDER BY p.CreatedAt DESC
          LIMIT 50
        ");
        $stmt->bind_param("iss", $CategoryID, $like, $like);
    } elseif ($CategoryID > 0) {
        $stmt = $conn->prepare("
          SELECT p.PostID, p.Title, p.CreatedAt, u.Username
          FROM Posts p
          JOIN Users u ON u.UserID = p.UserID
          WHERE p.CategoryID = ?
          ORDER BY p.CreatedAt DESC
          LIMIT 50
        ");
        $stmt->bind_param("i", $CategoryID);
    } else {
        $stmt = $conn->prepare("
          SELECT p.PostID, p.Title, p.CreatedAt, u.Username
          FROM Posts p
          JOIN Users u ON u.UserID = p.UserID
          WHERE p.Title LIKE ? OR p.Content LIKE ?
          ORDER BY p.CreatedAt DESC
          LIMIT 50
        ");
        $stmt->bind_param("ss", $like, $like);
    }

    $stmt->execute();
    $results = stmt_fetch_all($stmt);
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Search</title></head>
<body>
<h1>Search</h1>
<p><a href="index.php">← Back</a></p>

<form method="get" action="search.php">
  <input name="q" value="<?= e($q) ?>" placeholder="keyword">
  <input name="CategoryID" value="<?= (int)$CategoryID ?>" placeholder="CategoryID (optional)">
  <button type="submit">Search</button>
</form>

<?php if ($q !== '' || $CategoryID > 0): ?>
  <h2>Results</h2>
  <?php if (!$results): ?>
    <p>No results.</p>
  <?php else: ?>
    <ul>
    <?php foreach ($results as $r): ?>
      <li>
        <a href="post.php?PostID=<?= (int)$r['PostID'] ?>"><?= e($r['Title']) ?></a>
        — <?= e($r['Username']) ?> (<?= e($r['CreatedAt']) ?>)
      </li>
    <?php endforeach; ?>
    </ul>
  <?php endif; ?>
<?php endif; ?>

</body></html>
