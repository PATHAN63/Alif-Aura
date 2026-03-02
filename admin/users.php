<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id && $id != $_SESSION['user_id']) {
        $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'customer'")->execute([$id]);
    }
    header('Location: users.php');
    exit;
}

$users = $pdo->query("SELECT id, name, email, role, created_at FROM users ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - Admin - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin-body">
<div class="admin-layout">
    <aside class="admin-sidebar">
        <h3 style="color:#D4AF37;margin-bottom:1.5rem">Alif-Aura Admin</h3>
        <a href="dashboard.php">Dashboard</a>
        <a href="products.php">Products</a>
        <a href="orders.php">Orders</a>
        <a href="users.php" class="active">Users</a>
        <a href="../index.php">View Site</a>
        <a href="../logout.php">Logout</a>
    </aside>
    <main class="admin-content">
        <h1>Users</h1>
        <table class="cart-table">
            <thead>
                <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Joined</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= htmlspecialchars($u['name']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= $u['role'] ?></td>
                    <td><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
                    <td>
                        <?php if ($u['role'] === 'customer' && $u['id'] != $_SESSION['user_id']): ?>
                        <form method="POST" style="display:inline" onsubmit="return confirm('Delete user?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                            <button type="submit" style="background:none;border:none;color:#e74c3c;cursor:pointer">Delete</button>
                        </form>
                        <?php else: ?>—<?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</div>
</body>
</html>
