<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['order_id'] ?? 0);
    $status = sanitize($_POST['status'] ?? '');
    if ($id && in_array($status, ['pending','processing','shipped','delivered','cancelled'])) {
        $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?")->execute([$status, $id]);
    }
    header('Location: orders.php');
    exit;
}

$orders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Admin - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin-body">
<div class="admin-layout">
    <aside class="admin-sidebar">
        <h3 style="color:#D4AF37;margin-bottom:1.5rem">Alif-Aura Admin</h3>
        <a href="dashboard.php">Dashboard</a>
        <a href="products.php">Products</a>
        <a href="orders.php" class="active">Orders</a>
        <a href="users.php">Users</a>
        <a href="../index.php">View Site</a>
        <a href="../logout.php">Logout</a>
    </aside>
    <main class="admin-content">
        <h1>Orders</h1>
        <table class="cart-table">
            <thead>
                <tr><th>ID</th><th>Customer</th><th>Phone</th><th>Total</th><th>Status</th><th>Date</th><th>Action</th></tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                <tr>
                    <td>#<?= $o['id'] ?></td>
                    <td><?= htmlspecialchars($o['guest_name'] ?: 'User #'.$o['user_id']) ?></td>
                    <td><?= htmlspecialchars($o['guest_phone'] ?? '-') ?></td>
                    <td><?= formatPrice($o['total']) ?></td>
                    <td><?= $o['status'] ?></td>
                    <td><?= date('M j, Y H:i', strtotime($o['created_at'])) ?></td>
                    <td>
                        <form method="POST" style="display:inline">
                            <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                            <select name="status" onchange="this.form.submit()">
                                <option value="pending" <?= $o['status']==='pending'?'selected':'' ?>>Pending</option>
                                <option value="processing" <?= $o['status']==='processing'?'selected':'' ?>>Processing</option>
                                <option value="shipped" <?= $o['status']==='shipped'?'selected':'' ?>>Shipped</option>
                                <option value="delivered" <?= $o['status']==='delivered'?'selected':'' ?>>Delivered</option>
                                <option value="cancelled" <?= $o['status']==='cancelled'?'selected':'' ?>>Cancelled</option>
                            </select>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</div>
</body>
</html>
