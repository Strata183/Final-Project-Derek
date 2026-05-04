<?php
require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/csrf.php';

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate_post();

    $Username = trim($_POST['Username'] ?? '');
    $Password = $_POST['Password'] ?? '';

    $stmt = $conn->prepare("SELECT UserID, Username, Password, Admin FROM Users WHERE Username = ?");
    $stmt->bind_param("s", $Username);
    $stmt->execute();
    $user = stmt_fetch_one($stmt);

    if (!$user || !password_verify($Password, $user['Password'])) {
        $errors[] = "Invalid username or password.";
    } else {
        session_regenerate_id(true);
        $_SESSION['UserID'] = (int)$user['UserID'];
        $_SESSION['Username'] = $user['Username'];
        $_SESSION['Admin'] = (int)$user['Admin'];

        redirect('index.php');
    }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Login</title><link rel="stylesheet" href="styles.css"></head>
<body>
<h1>Login</h1>

<?php if ($errors): ?>
<ul><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul>
<?php endif; ?>

<form method="post" action="login.php">
  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
  <label>Username <input name="Username" required></label><br>
  <label>Password <input type="password" name="Password" required></label><br>
  <button type="submit">Login</button>
</form>

<p><a href="register.php">Register</a></p>
</body></html>
