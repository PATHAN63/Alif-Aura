<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $input['action'] ?? $_POST['action'] ?? '';

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if ($action === 'add') {
    $productId = (int) ($input['product_id'] ?? $_POST['product_id'] ?? 0);
    $quantity = (int) ($input['quantity'] ?? $_POST['quantity'] ?? 1);
    if (!$productId) {
        echo json_encode(['success' => false]);
        exit;
    }
    $stmt = $pdo->prepare("SELECT id, name, price, sale_price FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    if (!$product) {
        echo json_encode(['success' => false]);
        exit;
    }
    $price = $product['sale_price'] ?: $product['price'];
    $key = $productId . '_' . ($input['size'] ?? '') . '_' . ($input['color'] ?? '');
    if (isset($_SESSION['cart'][$key])) {
        $_SESSION['cart'][$key]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$key] = [
            'product_id' => $productId,
            'name' => $product['name'],
            'price' => $price,
            'quantity' => $quantity,
            'size' => $input['size'] ?? '',
            'color' => $input['color'] ?? '',
        ];
    }
    echo json_encode(['success' => true, 'count' => getCartCount()]);
} else {
    echo json_encode(['success' => false]);
}
