<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Login required']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$productId = (int) ($input['product_id'] ?? 0);
$userId = $_SESSION['user_id'];

if (!$productId) {
    echo json_encode(['success' => false]);
    exit;
}

$stmt = $pdo->prepare("SELECT 1 FROM wishlist WHERE user_id = ? AND product_id = ?");
$stmt->execute([$userId, $productId]);
$exists = $stmt->fetch();

if ($exists) {
    $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?")->execute([$userId, $productId]);
    echo json_encode(['success' => true, 'in_wishlist' => false]);
} else {
    $pdo->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)")->execute([$userId, $productId]);
    echo json_encode(['success' => true, 'in_wishlist' => true]);
}
