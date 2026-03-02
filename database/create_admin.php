<?php
/**
 * Create/update admin user. Run: php database/create_admin.php
 */
require_once __DIR__ . '/../includes/db.php';

$email = 'admin@alifaura.com';
$password = 'admin123';
$hash = password_hash($password, PASSWORD_BCRYPT);

$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    $pdo->prepare("UPDATE users SET password = ? WHERE email = ?")->execute([$hash, $email]);
    echo "Admin password updated.\n";
} else {
    $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin')")->execute(['Admin', $email, $hash]);
    echo "Admin created.\n";
}
echo "Login: $email / $password\n";
