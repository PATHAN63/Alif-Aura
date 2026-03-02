<?php
/**
 * One-time setup: Create admin user
 * Visit: yoursite.com/setup.php
 * Then delete this file for security.
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

$email = 'admin@alifaura.com';
$password = 'admin123';
$hash = password_hash($password, PASSWORD_BCRYPT);

$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    $pdo->prepare("UPDATE users SET password = ?, role = 'admin' WHERE email = ?")->execute([$hash, $email]);
    $msg = "Admin password updated successfully!";
} else {
    $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin')")->execute(['Admin', $email, $hash]);
    $msg = "Admin created successfully!";
}
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Setup - Alif-Aura</title>
<style>body{font-family:sans-serif;background:#0F0F0F;color:#fff;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;}
.box{background:#1a1a1a;padding:2rem;border:1px solid #D4AF37;max-width:400px;text-align:center;}
h1{color:#D4AF37;} .success{color:#2ecc71;} .cred{background:#0F0F0F;padding:1rem;margin:1rem 0;font-family:monospace;}
a{color:#D4AF37;}</style>
</head>
<body>
<div class="box">
    <h1>Alif-Aura Setup</h1>
    <p class="success"><?= $msg ?></p>
    <p>Admin Login:</p>
    <div class="cred">Email: <?= $email ?><br>Password: <?= $password ?></div>
    <p><a href="login.php">Go to Login</a></p>
    <p style="font-size:0.8rem;color:#8a8a8a">Delete setup.php for security after use.</p>
</div>
</body>
</html>
