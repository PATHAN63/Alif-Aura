<?php
$pageTitle = 'Cart';
require_once 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity = (int) ($_POST['quantity'] ?? 1);
        $size = sanitize($_POST['size'] ?? '');
        $color = sanitize($_POST['color'] ?? '');
        if ($productId) {
            $stmt = $pdo->prepare("SELECT id, name, price, sale_price FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $product = $stmt->fetch();
            if ($product) {
                $price = $product['sale_price'] ?: $product['price'];
                $key = $productId . '_' . $size . '_' . $color;
                if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
                if (isset($_SESSION['cart'][$key])) {
                    $_SESSION['cart'][$key]['quantity'] += $quantity;
                } else {
                    $_SESSION['cart'][$key] = [
                        'product_id' => $productId,
                        'name' => $product['name'],
                        'price' => $price,
                        'quantity' => $quantity,
                        'size' => $size,
                        'color' => $color,
                    ];
                }
            }
        }
        header('Location: cart.php');
        exit;
    }
    if ($action === 'update') {
        $key = $_POST['key'] ?? '';
        $qty = (int) ($_POST['quantity'] ?? 0);
        if (isset($_SESSION['cart'][$key])) {
            if ($qty <= 0) unset($_SESSION['cart'][$key]);
            else $_SESSION['cart'][$key]['quantity'] = $qty;
        }
        header('Location: cart.php');
        exit;
    }
    if ($action === 'remove') {
        $key = $_POST['key'] ?? '';
        unset($_SESSION['cart'][$key]);
        header('Location: cart.php');
        exit;
    }
}

$cart = $_SESSION['cart'] ?? [];
$total = 0;
?>

<div class="section">
    <h1 class="section-title">Shopping Cart</h1>
    <?php if (empty($cart)): ?>
        <p style="text-align:center;padding:4rem">Your cart is empty. <a href="shop.php" class="btn-gold" style="display:inline-block;margin-top:1rem">Continue Shopping</a></p>
    <?php else: ?>
    <table class="cart-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cart as $key => $item): 
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
                $stmt = $pdo->prepare("SELECT images FROM products WHERE id = ?");
                $stmt->execute([$item['product_id']]);
                $p = $stmt->fetch();
                $imgs = json_decode($p['images'] ?? '[]', true);
                $img = !empty($imgs) ? $imgs[0] : 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=100&q=80';
            ?>
            <tr>
                <td>
                    <a href="product.php?id=<?= $item['product_id'] ?>">
                        <img src="<?= htmlspecialchars($img) ?>" alt="" class="cart-item-img">
                        <strong><?= htmlspecialchars($item['name']) ?></strong>
                        <?php if ($item['size']): ?><br><small>Size: <?= $item['size'] ?></small><?php endif; ?>
                    </a>
                </td>
                <td><?= formatPrice($item['price']) ?></td>
                <td>
                    <form method="POST" style="display:inline">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="key" value="<?= htmlspecialchars($key) ?>">
                        <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" class="qty-input" onchange="this.form.submit()">
                    </form>
                </td>
                <td><?= formatPrice($subtotal) ?></td>
                <td>
                    <form method="POST" style="display:inline" onsubmit="return confirm('Remove item?')">
                        <input type="hidden" name="action" value="remove">
                        <input type="hidden" name="key" value="<?= htmlspecialchars($key) ?>">
                        <button type="submit" style="background:none;border:none;color:#D4AF37;cursor:pointer">Remove</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="cart-total">
        <strong>Total: <span class="total"><?= formatPrice($total) ?></span></strong>
        <br><br>
        <a href="checkout.php" class="btn-gold">Proceed to Checkout</a>
    </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
