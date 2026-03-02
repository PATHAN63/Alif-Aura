<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$id = (int) ($_GET['id'] ?? 0);
if (!$id) {
    header('Location: products.php');
    exit;
}
$product = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$product->execute([$id]);
$product = $product->fetch();
if (!$product) {
    header('Location: products.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $category_id = (int) ($_POST['category_id'] ?? 0);
    $description = sanitize($_POST['description'] ?? '');
    $price = (float) ($_POST['price'] ?? 0);
    $sale_price = !empty($_POST['sale_price']) ? (float) $_POST['sale_price'] : null;
    $sizes = sanitize($_POST['sizes'] ?? 'S,M,L,XL');
    $colors = sanitize($_POST['colors'] ?? '');
    $stock = (int) ($_POST['stock'] ?? 100);
    $featured = isset($_POST['featured']) ? 1 : 0;
    $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower($name));

    $images = json_decode($product['images'] ?? '[]', true) ?: [];
    if (!empty($_FILES['images']['name'][0])) {
        $uploadDir = __DIR__ . '/../uploads/products/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
            if ($_FILES['images']['error'][$i] === 0) {
                $ext = pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION) ?: 'jpg';
                $fname = uniqid() . '.' . $ext;
                if (move_uploaded_file($tmp, $uploadDir . $fname)) {
                    $images[] = 'uploads/products/' . $fname;
                }
            }
        }
    }
    $imgJson = json_encode($images ?: ['images/placeholder.jpg']);

    $pdo->prepare("UPDATE products SET name=?, slug=?, category_id=?, description=?, price=?, sale_price=?, sizes=?, colors=?, stock=?, featured=?, images=? WHERE id=?")
        ->execute([$name, $slug, $category_id, $description, $price, $sale_price, $sizes, $colors, $stock, $featured, $imgJson, $id]);
    header('Location: products.php');
    exit;
}

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Admin - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin-body">
<div class="admin-layout">
    <aside class="admin-sidebar">
        <h3 style="color:#D4AF37;margin-bottom:1.5rem">Alif-Aura Admin</h3>
        <a href="dashboard.php">Dashboard</a>
        <a href="products.php" class="active">Products</a>
        <a href="orders.php">Orders</a>
        <a href="users.php">Users</a>
        <a href="../index.php">View Site</a>
        <a href="../logout.php">Logout</a>
    </aside>
    <main class="admin-content">
        <h1>Edit Product</h1>
        <form method="POST" enctype="multipart/form-data" style="max-width:600px">
            <div class="form-group"><label>Name</label><input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required></div>
            <div class="form-group"><label>Category</label>
                <select name="category_id" required>
                    <?php foreach ($categories as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $c['id']==$product['category_id']?'selected':'' ?>><?= $c['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label>Description</label><textarea name="description" rows="3"><?= htmlspecialchars($product['description'] ?? '') ?></textarea></div>
            <div class="form-group"><label>Price (PKR)</label><input type="number" name="price" step="0.01" value="<?= $product['price'] ?>" required></div>
            <div class="form-group"><label>Sale Price (PKR)</label><input type="number" name="sale_price" step="0.01" value="<?= $product['sale_price'] ?? '' ?>"></div>
            <div class="form-group"><label>Sizes</label><input type="text" name="sizes" value="<?= htmlspecialchars($product['sizes'] ?? 'S,M,L,XL') ?>"></div>
            <div class="form-group"><label>Colors</label><input type="text" name="colors" value="<?= htmlspecialchars($product['colors'] ?? '') ?>"></div>
            <div class="form-group"><label>Stock</label><input type="number" name="stock" value="<?= $product['stock'] ?>"></div>
            <div class="form-group"><label>Add More Images</label><input type="file" name="images[]" multiple accept="image/*"></div>
            <div class="form-group"><label><input type="checkbox" name="featured" <?= $product['featured']?'checked':'' ?>> Featured</label></div>
            <button type="submit" class="btn-gold">Save</button>
            <a href="products.php" class="btn-outline">Cancel</a>
        </form>
    </main>
</div>
</body>
</html>
