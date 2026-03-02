<?php
$pageTitle = 'Register';
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (isLoggedIn()) { header('Location: ' . BASE_PATH . '/index.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) { $error = 'Invalid request. Please try again.'; }
    else {
        $name = sanitize($_POST['name'] ?? '');
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        if (!$name || !$email || !$password) $error = 'Please fill all fields.';
        elseif (strlen($password) < 6) $error = 'Password must be at least 6 characters.';
        elseif ($password !== $confirm) $error = 'Passwords do not match.';
        else {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) $error = 'Email already registered.';
            else {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)")->execute([$name, $email, $hash]);
                session_regenerate_id(true);
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                $_SESSION['role'] = 'customer';
                header('Location: ' . BASE_PATH . '/index.php');
                exit;
            }
        }
    }
}

require_once 'includes/header.php';
?>
<div class="auth-page section fade-in">
    <div class="auth-box">
        <h1>Create Account</h1>
        <p class="auth-tagline"><?= SITE_TAGLINE ?></p>
        <?php if ($error): ?><p class="auth-error"><?= sanitize($error) ?></p><?php endif; ?>
        <form method="POST" class="auth-form">
            <?= csrf_field() ?>
            <div class="form-group floating">
                <input type="text" name="name" id="name" required value="<?= sanitize($_POST['name'] ?? '') ?>">
                <label for="name">Full Name</label>
            </div>
            <div class="form-group floating">
                <input type="email" name="email" id="email" required value="<?= sanitize($_POST['email'] ?? '') ?>">
                <label for="email">Email</label>
            </div>
            <div class="form-group floating">
                <input type="password" name="password" id="password" required minlength="6">
                <label for="password">Password</label>
            </div>
            <div class="form-group floating">
                <input type="password" name="confirm_password" id="confirm" required>
                <label for="confirm">Confirm Password</label>
            </div>
            <button type="submit" class="btn-gold" style="width:100%">Sign Up</button>
        </form>
        <p class="auth-switch">Already have an account? <a href="<?= BASE_PATH ?>/login.php">Login</a></p>
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
.floating label{position:absolute;left:1rem;top:1rem;color:#8a8a8a;pointer-events:none;transition:0.2s}
.floating input:focus+label,.floating input:not(:placeholder-shown)+label{transform:translateY(-1.5rem);font-size:0.75rem}
.floating input::placeholder{opacity:0}
</style>
<?php require_once 'includes/footer.php'; ?>
