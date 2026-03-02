<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$totalSales = $pdo->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status IN ('shipped','delivered')")->fetchColumn();
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE role='customer'")->fetchColumn();
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$recentOrders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 10")->fetchAll();

$pageTitle = 'Admin Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin-body">
<div class="admin-layout">
    <aside class="admin-sidebar">
        <h3 style="color:#D4AF37;margin-bottom:1.5rem">Alif-Aura Admin</h3>
        <a href="dashboard.php" class="active">Dashboard</a>
        <a href="products.php">Products</a>
        <a href="orders.php">Orders</a>
        <a href="users.php">Users</a>
        <a href="../index.php">View Site</a>
        <a href="../logout.php">Logout</a>
    </aside>
    <main class="admin-content">
        <h1>Dashboard</h1>
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?= CURRENCY . ' ' . number_format($totalSales) ?></h3>
                <p>Total Sales</p>
            </div>
            <div class="stat-card">
                <h3><?= $totalOrders ?></h3>
                <p>Total Orders</p>
            </div>
            <div class="stat-card">
                <h3><?= $totalUsers ?></h3>
                <p>Customers</p>
            </div>
            <div class="stat-card">
                <h3><?= $totalProducts ?></h3>
                <p>Products</p>
            </div>
        </div>
        <h2 style="margin-top:2rem">Recent Orders</h2>
        <table class="cart-table">
            <thead>
                <tr><th>ID</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th></tr>
            </thead>
            <tbody>
                <?php foreach ($recentOrders as $o): ?>
                <tr>
                    <td>#<?= $o['id'] ?></td>
                    <td><?= htmlspecialchars($o['guest_name'] ?: 'User #'.$o['user_id']) ?></td>
                    <td><?= formatPrice($o['total']) ?></td>
                    <td><?= $o['status'] ?></td>
                    <td><?= date('M j, Y', strtotime($o['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</div>
</body>
</html>
