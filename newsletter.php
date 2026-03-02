<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    if ($email) {
        try {
            $stmt = $pdo->prepare("INSERT INTO newsletter (email) VALUES (?) ON DUPLICATE KEY UPDATE email=email");
            $stmt->execute([$email]);
        } catch (PDOException $e) {}
    }
}
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
exit;
