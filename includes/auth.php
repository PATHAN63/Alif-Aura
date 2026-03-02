<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

function secureSession() {
    if (session_status() === PHP_SESSION_NONE) return;
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
    ini_set('session.use_strict_mode', 1);
}

function checkBruteForce($pdo, $email) {
    try {
        $stmt = $pdo->prepare("SELECT locked_until FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $u = $stmt->fetch();
        return $u && !empty($u['locked_until']) && strtotime($u['locked_until']) > time();
    } catch (Exception $e) { return false; }
}

function recordLoginAttempt($pdo, $email, $success) {
    try {
        if ($success) {
            $pdo->prepare("UPDATE users SET login_attempts = 0, locked_until = NULL WHERE email = ?")->execute([$email]);
            return;
        }
        $pdo->prepare("UPDATE users SET login_attempts = COALESCE(login_attempts, 0) + 1 WHERE email = ?")->execute([$email]);
        $attempts = $pdo->prepare("SELECT login_attempts FROM users WHERE email = ?");
        $attempts->execute([$email]);
        $n = (int)($attempts->fetchColumn() ?? 0);
        if ($n >= 5) $pdo->prepare("UPDATE users SET locked_until = DATE_ADD(NOW(), INTERVAL 15 MINUTE) WHERE email = ?")->execute([$email]);
    } catch (Exception $e) {}
}
