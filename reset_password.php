<?php
$pageTitle = 'Reset Password';
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$error = ''; $success = false; $validToken = false;
$token = sanitize($_GET['token'] ?? '');
$email = filter_var(trim($_GET['email'] ?? ''), FILTER_VALIDATE_EMAIL);

if ($token && $email) {
    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND reset_token = ? AND reset_expires > NOW()");
        $stmt->execute([$email, $token]);
        $validToken = (bool)$stmt->fetch();
    } catch (Exception $e) {}
}
if ($validToken && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) $error = 'Invalid request.';
    else {
        $pass = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        if (strlen($pass) < 6) $error = 'Password must be at least 6 characters.';
        elseif ($pass !== $confirm) $error = 'Passwords do not match.';
        else {
            $hash = password_hash($pass, PASSWORD_BCRYPT);
            $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE email = ?")->execute([$hash, $email]);
            $success = true;
        }
    }
}

require_once 'includes/header.php';
?>
<div class="auth-page section fade-in">
    <div class="auth-box">
        <h1>Reset Password</h1>
        <?php if ($success): ?>
            <p class="auth-success" style="color:#2ecc71">Password updated. <a href="<?= BASE_PATH ?>/login.php">Login</a></p>
        <?php elseif ($validToken): ?>
            <?php if ($error): ?><p class="auth-error"><?= sanitize($error) ?></p><?php endif; ?>
            <form method="POST" class="auth-form">
                <?= csrf_field() ?>
                <div class="form-group floating">
                    <input type="password" name="password" id="password" required minlength="6" placeholder=" ">
                    <label for="password">New Password</label>
                </div>
                <div class="form-group floating">
                    <input type="password" name="confirm_password" id="confirm" required placeholder=" ">
                    <label for="confirm">Confirm Password</label>
                </div>
                <button type="submit" class="btn-gold" style="width:100%">Update Password</button>
            </form>
        <?php else: ?>
            <p class="auth-error"><?= sanitize($error ?: 'Invalid or expired link.') ?></p>
            <p><a href="<?= BASE_PATH ?>/login.php">Back to Login</a></p>
        <?php endif; ?>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>
