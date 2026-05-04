<?php
require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';

require_login();

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate_post();

    $currentPassword = $_POST['CurrentPassword'] ?? '';
    $newPassword = $_POST['NewPassword'] ?? '';
    $confirmPassword = $_POST['ConfirmPassword'] ?? '';

    if (strlen($newPassword) < 8) {
        $errors[] = "New password must be at least 8 characters.";
    }
    if ($newPassword !== $confirmPassword) {
        $errors[] = "New passwords do not match.";
    }

    if (!$errors) {
        $UserID = current_user_id();
        $stmt = $conn->prepare("SELECT Password FROM Users WHERE UserID = ?");
        $stmt->bind_param("i", $UserID);
        $stmt->execute();
        $user = stmt_fetch_one($stmt);

        if (!$user || !password_verify($currentPassword, $user['Password'])) {
            $errors[] = "Current password is incorrect.";
        } else {
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE Users SET Password = ? WHERE UserID = ?");
            $stmt->bind_param("si", $hash, $UserID);
            $stmt->execute();
            $success = "Password updated.";
        }
    }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Change Password</title><link rel="stylesheet" href="styles.css"></head>
<body>
<h1>Change Password</h1>
<p><a href="index.php">Back</a></p>

<?php if ($errors): ?>
<ul><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul>
<?php endif; ?>

<?php if ($success): ?>
<p><?= e($success) ?></p>
<?php endif; ?>

<form method="post" action="change_password.php">
  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
  <label>Current password <input type="password" name="CurrentPassword" required></label>
  <label>New password <input type="password" name="NewPassword" required minlength="8"></label>
  <label>Confirm new password <input type="password" name="ConfirmPassword" required minlength="8"></label>
  <button type="submit">Update Password</button>
</form>
</body></html>
