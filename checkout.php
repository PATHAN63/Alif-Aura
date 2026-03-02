<?php
$pageTitle = 'Checkout';
require_once 'includes/header.php';
require_once 'includes/send_order_email.php';

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) { header('Location: ' . BASE_PATH . '/cart.php'); exit; }

$total = 0;
foreach ($cart as $item) $total += $item['price'] * $item['quantity'];

$success = false; $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) { $error = 'Invalid request. Please try again.'; }
    else {
        $name = sanitize($_POST['name'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $address = sanitize($_POST['address'] ?? '');
        $payment = sanitize($_POST['payment'] ?? 'cod');
        if ($name && $phone && $address) {
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, guest_name, guest_phone, guest_email, shipping_address, total, payment_method, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
            $stmt->execute([isLoggedIn() ? $_SESSION['user_id'] : null, $name, $phone, $email, $address, $total, $payment]);
            $orderId = $pdo->lastInsertId();
            foreach ($cart as $item) {
                $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, price, size, color) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$orderId, $item['product_id'], $item['name'], $item['quantity'], $item['price'], $item['size'] ?? '', $item['color'] ?? '']);
            }
            sendOrderEmails($pdo, $orderId, $email, $name, $address, $cart, $total, $payment);
            $_SESSION['cart'] = [];
            $success = true;
        }
    }
}
?>

<?php if ($success): ?>
<div class="section" id="order-success">
    <div class="success-animation">
        <div class="success-checkmark">✓</div>
        <h2>Order Confirmed!</h2>
        <p>Thank you for your purchase. We will contact you soon.</p>
        <a href="shop.php" class="btn-gold">Continue Shopping</a>
    </div>
</div>
<?php else: ?>
<div class="section checkout-section">
    <h1 class="section-title">Checkout</h1>
    <?php if (!empty($error)): ?><p class="auth-error" style="text-align:center;color:#e74c3c"><?= sanitize($error) ?></p><?php endif; ?>
    <form method="POST" class="checkout-form">
        <?= csrf_field() ?>
        <div class="checkout-grid">
            <div class="checkout-form-area">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="name" value="<?= isLoggedIn() ? htmlspecialchars($_SESSION['user_name'] ?? '') : '' ?>" required>
                </div>
                <div class="form-group">
                    <label>Phone *</label>
                    <input type="tel" name="phone" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= isLoggedIn() ? htmlspecialchars($_SESSION['user_email'] ?? '') : '' ?>">
                </div>
                <div class="form-group">
                    <label>Shipping Address *</label>
                    <textarea name="address" rows="4" required></textarea>
                </div>
                <div class="form-group">
                    <label>Payment Method</label>
                    <select name="payment">
                        <option value="cod">Cash on Delivery (COD)</option>
                        <option value="online">Online Payment</option>
                    </select>
                </div>
            </div>
            <div class="checkout-summary">
                <h3>Order Summary</h3>
                <?php foreach ($cart as $item): ?>
                <p><?= htmlspecialchars($item['name']) ?> x <?= $item['quantity'] ?> — <?= formatPrice($item['price'] * $item['quantity']) ?></p>
                <?php endforeach; ?>
                <p class="total"><strong>Total: <?= formatPrice($total) ?></strong></p>
                <button type="submit" class="btn-gold" style="width:100%;margin-top:1rem">Place Order</button>
            </div>
        </div>
    </form>
</div>
<?php endif; ?>

<style>
.success-animation { text-align: center; padding: 4rem 2rem; }
.success-checkmark { width: 80px; height: 80px; background: #D4AF37; color: #0F0F0F; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin: 0 auto 1.5rem; animation: scaleIn 0.5s ease; }
@keyframes scaleIn { from { transform: scale(0); opacity: 0; } to { transform: scale(1); opacity: 1; } }
.checkout-grid { display: grid; grid-template-columns: 1fr 350px; gap: 3rem; max-width: 1000px; margin: 0 auto; }
.checkout-summary { background: #1a1a1a; padding: 2rem; border: 1px solid rgba(212,175,55,0.2); }
@media (max-width: 768px) { .checkout-grid { grid-template-columns: 1fr; } }
</style>

<?php require_once 'includes/footer.php'; ?>
