<?php
$pageTitle = 'Login';
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

if (isLoggedIn()) { header('Location: ' . BASE_PATH . '/index.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) { $error = 'Invalid request. Please try again.'; }
    else {
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';
        if (!$email || !$password) $error = 'Please fill all fields.';
        elseif (checkBruteForce($pdo, $email)) $error = 'Too many attempts. Try again in 15 minutes.';
        else {
            $stmt = $pdo->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                recordLoginAttempt($pdo, $email, true);
                $redirect = $_GET['redirect'] ?? ($user['role'] === 'admin' ? BASE_PATH . '/admin/dashboard.php' : BASE_PATH . '/index.php');
                header('Location: ' . $redirect);
                exit;
            }
            recordLoginAttempt($pdo, $email, false);
            $error = 'Invalid email or password.';
        }
    }
}

require_once 'includes/header.php';
?>
<div class="auth-page section fade-in">
    <div class="auth-box">
        <h1>Welcome Back</h1>
        <p class="auth-tagline"><?= SITE_TAGLINE ?></p>
        <?php if ($error): ?><p class="auth-error"><?= sanitize($error) ?></p><?php endif; ?>
        <form method="POST" class="auth-form">
            <?= csrf_field() ?>
            <div class="form-group floating">
                <input type="email" name="email" id="email" required autocomplete="email" value="<?= sanitize($_POST['email'] ?? '') ?>">
                <label for="email">Email</label>
            </div>
            <div class="form-group floating">
                <input type="password" name="password" id="password" required>
                <label for="password">Password</label>
            </div>
            <button type="submit" class="btn-gold" style="width:100%">Login</button>
        </form>
        <p class="auth-switch">Don't have an account? <a href="<?= BASE_PATH ?>/register.php">Sign up</a> | <a href="<?= BASE_PATH ?>/forgot_password.php">Forgot password?</a></p>
    </div>
</div>
<style>
.auth-page{min-height:80vh;display:flex;align-items:center;justify-content:center;padding:4rem 2rem}
.auth-box{max-width:400px;width:100%;background:#1a1a1a;padding:3rem;border:1px solid rgba(212,175,55,0.3)}
.auth-box h1{font-family:var(--font-display);font-size:2rem;margin-bottom:0.5rem;color:#D4AF37}
.auth-tagline{color:#8a8a8a;margin-bottom:2rem;font-size:0.9rem}
.auth-error{color:#e74c3c;margin-bottom:1rem}
.auth-form .form-group{margin-bottom:1.5rem}
.auth-form input{width:100%;padding:1rem;background:#0F0F0F;border:1px solid rgba(212,175,55,0.3);color:#fff}
.auth-switch{margin-top:1.5rem;text-align:center}
.auth-switch a{color:#D4AF37}
.floating{position:relative}
.floating input:focus+label,.floating input:not(:placeholder-shown)+label{transform:translateY(-1.5rem);font-size:0.75rem}
.floating label{position:absolute;left:1rem;top:1rem;color:#8a8a8a;pointer-events:none;transition:0.2s}
.floating input::placeholder{opacity:0}
</style>
<?php require_once 'includes/footer.php'; ?>
