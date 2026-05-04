<?php
require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/csrf.php';

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate_post();

    $Username = trim($_POST['Username'] ?? '');
    $Email = trim($_POST['Email'] ?? '');
    $Password = $_POST['Password'] ?? '';

    if ($Username === '' || strlen($Username) > 50) $errors[] = "Username required (max 50).";
    if ($Email === '' || !filter_var($Email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email required.";
    if (strlen($Password) < 8) $errors[] = "Password must be at least 8 characters.";

    if (!$errors) {
        $stmt = $conn->prepare("
            SELECT UserID
            FROM Users
            WHERE (Username = ? OR Email = ?) AND Password IS NOT NULL AND Password <> ''
            LIMIT 1
        ");
        $stmt->bind_param("ss", $Username, $Email);
        $stmt->execute();
        if (stmt_fetch_one($stmt)) {
            $errors[] = "That username or email is already registered.";
        }
    }

    if (!$errors) {
        $hash = password_hash($Password, PASSWORD_DEFAULT);
        $Admin = 0;

        $stmt = $conn->prepare("INSERT INTO Users (Username, Password, Email, Admin) VALUES (?, ?, ?, ?)");
        try {
            $stmt->bind_param("sssi", $Username, $hash, $Email, $Admin);
            $stmt->execute();

            $_SESSION['UserID'] = $conn->insert_id;
            $_SESSION['Username'] = $Username;
            $_SESSION['Admin'] = 0;

            redirect('index.php');
        } catch (mysqli_sql_exception $e) {
            // If you have UNIQUE constraints on Username/Email, handle duplicates nicely.
            if ($e->getCode() === 1062) $errors[] = "Username or email already exists.";
            else throw $e;
        }
    }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Register</title><link rel="stylesheet" href="styles.css"></head>
<body>
<h1>Register</h1>

<?php if ($errors): ?>
<ul><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul>
<?php endif; ?>

<form method="post" action="register.php">
  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
  <label>Username <input name="Username" required maxlength="50"></label><br>
  <label>Email <input name="Email" required></label><br>
  <label>Password <input type="password" name="Password" required minlength="8"></label><br>
  <button type="submit">Create account</button>
</form>

<p><a href="login.php">Login</a></p>
</body></html>
