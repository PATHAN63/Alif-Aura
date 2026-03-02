<?php
function sanitize($input) {
    if (is_array($input)) return array_map('sanitize', $input);
    return htmlspecialchars(trim($input ?? ''), ENT_QUOTES, 'UTF-8');
}

function sanitize_input($key, $default = '') {
    $val = $_POST[$key] ?? $_GET[$key] ?? $default;
    return sanitize($val);
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function csrf_verify() {
    $token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_PATH . '/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: ' . BASE_PATH . '/index.php');
        exit;
    }
}

function formatPrice($price) {
    return CURRENCY . ' ' . number_format((float)$price);
}

function getCartCount() {
    $count = 0;
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) $count += $item['quantity'] ?? 0;
    }
    return $count;
}

function getWishlistCount($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetchColumn();
}

function inWishlist($pdo, $userId, $productId) {
    $stmt = $pdo->prepare("SELECT 1 FROM wishlist WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$userId, $productId]);
    return (bool)$stmt->fetch();
}

function generateAlt($name, $context = '') {
    return trim($name . ' ' . $context . ' - Alif-Aura') ?: 'Alif-Aura product';
}
