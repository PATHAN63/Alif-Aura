<?php
$pageTitle = 'Forgot Password';
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$msg = ''; $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) { $error = 'Invalid request.'; }
    else {
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        if (!$email) { $error = 'Enter a valid email.'; }
        else {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $token = bin2hex(random_bytes(32));
                $exp = date('Y-m-d H:i:s', strtotime('+1 hour'));
                $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?")->execute([$token, $exp, $email]);
                $link = SITE_URL . (BASE_PATH ?: '') . '/reset_password.php?token=' . $token . '&email=' . urlencode($email);
                $msg = 'If an account exists, you will receive reset instructions. Link: ' . $link; // Replace with email send
            }
            $msg = 'If that email exists, you will receive reset instructions.';
        }
    }
}
require_once 'includes/header.php';
?>
<div class="auth-page section fade-in">
    <div class="auth-box">
        <h1>Forgot Password</h1>
        <?php if ($error): ?><p class="auth-error"><?= sanitize($error) ?></p><?php endif; ?>
        <?php if ($msg): ?><p class="auth-success" style="color:#2ecc71"><?= sanitize($msg) ?></p><?php endif; ?>
        <form method="POST" class="auth-form">
            <?= csrf_field() ?>
            <div class="form-group floating">
                <input type="email" name="email" id="email" required placeholder=" ">
                <label for="email">Email</label>
            </div>
            <button type="submit" class="btn-gold" style="width:100%">Send Reset Link</button>
        </form>
        <p class="auth-switch"><a href="<?= BASE_PATH ?>/login.php">Back to Login</a></p>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>
