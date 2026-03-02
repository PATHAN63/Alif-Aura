<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

 $error = '';
 $showAddModal = false;
 $formData = [];
 if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id) $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
    }
    if ($action === 'add' || $action === 'edit') {
        $id = (int) ($_POST['id'] ?? 0);
        $name = sanitize($_POST['name'] ?? '');
        $formData['name'] = $name;
        $category_id = (int) ($_POST['category_id'] ?? 0);
        $formData['category_id'] = $category_id;
        $description = sanitize($_POST['description'] ?? '');
        $formData['description'] = $description;
        $price = (float) ($_POST['price'] ?? 0);
        $formData['price'] = $price;
        $sale_price = !empty($_POST['sale_price']) ? (float) $_POST['sale_price'] : null;
        $formData['sale_price'] = $sale_price;
        $sizes = sanitize($_POST['sizes'] ?? 'S,M,L,XL');
        $formData['sizes'] = $sizes;
        $colors = sanitize($_POST['colors'] ?? '');
        $formData['colors'] = $colors;
        $stock = (int) ($_POST['stock'] ?? 100);
        $formData['stock'] = $stock;
        $featured = isset($_POST['featured']) ? 1 : 0;
        $formData['featured'] = $featured;
        $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower($name));
        // Handle image uploads
        $images = [];
        $uploadedCount = 0;
        if (!empty($_FILES['images']['name'][0])) {
            // validate files first (server-side)
            $uploadDir = __DIR__ . '/../uploads/products/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
                $err = $_FILES['images']['error'][$i];
                $nameOrig = $_FILES['images']['name'][$i] ?? '';
                if ($err === 0 && is_uploaded_file($tmp)) {
                    // basic size check (max 2MB) and image check
                    if ($_FILES['images']['size'][$i] > 2 * 1024 * 1024) {
                        $error = 'Each image must be <= 2MB.';
                        break;
                    }
                    $info = @getimagesize($tmp);
                    if ($info === false) { $error = 'One or more uploaded files are not valid images.'; break; }
                    $uploadedCount++;
                }
            }
        }

        // For new products, require 3-4 images
        if ($action === 'add') {
            if ($uploadedCount < 3) {
                $error = 'Please upload at least 3 images (maximum 4).';
            } elseif ($uploadedCount > 4) {
                $error = 'Please upload no more than 4 images.';
            }
        }

        // If validation passed, move files
        if (!$error && $uploadedCount > 0) {
            foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
                if ($_FILES['images']['error'][$i] === 0 && is_uploaded_file($tmp)) {
                    $ext = pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION) ?: 'jpg';
                    $fname = uniqid() . '.' . $ext;
                    if (move_uploaded_file($tmp, $uploadDir . $fname)) {
                        $images[] = 'uploads/products/' . $fname;
                    }
                }
            }
        }
        
        if ($action === 'edit' && $id) {
            $existing = $pdo->prepare("SELECT images FROM products WHERE id = ?");
            $existing->execute([$id]);
            $ex = $existing->fetch();
            if ($ex && $ex['images']) {
                $existingImgs = json_decode($ex['images'], true) ?: [];
                $images = array_merge($existingImgs, $images);
            }
            $imgJson = json_encode($images ?: ['images/placeholder.jpg']);
            $pdo->prepare("UPDATE products SET name=?, slug=?, category_id=?, description=?, price=?, sale_price=?, sizes=?, colors=?, stock=?, featured=?, images=? WHERE id=?")
                ->execute([$name, $slug, $category_id, $description, $price, $sale_price, $sizes, $colors, $stock, $featured, $imgJson, $id]);
        } else {
            if ($error) {
                // show add modal with previous values
                $showAddModal = true;
            } else {
                $imgJson = json_encode($images ?: ['images/placeholder.jpg']);
                $pdo->prepare("INSERT INTO products (name, slug, category_id, description, price, sale_price, sizes, colors, stock, featured, images) VALUES (?,?,?,?,?,?,?,?,?,?,?)")
                    ->execute([$name, $slug, $category_id, $description, $price, $sale_price, $sizes, $colors, $stock, $featured, $imgJson]);
            }
        }
    }
    if (!$showAddModal) {
        header('Location: products.php');
        exit;
    }
}

$products = $pdo->query("SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC")->fetchAll();
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Admin - <?= SITE_NAME ?></title>
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
        <h1>Products</h1>
        <button onclick="document.getElementById('productModal').style.display='block'" class="btn-gold" style="margin-bottom:1rem">Add Product</button>
        <table class="cart-table">
            <thead>
                <tr><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): 
                    $imgs = json_decode($p['images'] ?? '[]', true);
                    $img = !empty($imgs) ? $imgs[0] : '../images/placeholder.jpg';
                ?>
                <tr>
                    <td><img src="../<?= htmlspecialchars($img) ?>" alt="" style="width:50px;height:60px;object-fit:cover" onerror="this.src='https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=50'"></td>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td><?= htmlspecialchars($p['cat_name']) ?></td>
                    <td><?= formatPrice($p['sale_price'] ?: $p['price']) ?></td>
                    <td>
                        <a href="product_edit.php?id=<?= $p['id'] ?>" class="btn-outline" style="padding:0.3rem 0.6rem;font-size:0.8rem">Edit</a>
                        <form method="POST" style="display:inline" onsubmit="return confirm('Delete?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
                            <button type="submit" style="background:none;border:none;color:#e74c3c;cursor:pointer;padding:0.3rem">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div id="productModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.8);z-index:9999;overflow:auto;padding:2rem">
            <div style="max-width:600px;margin:2rem auto;background:#1a1a1a;padding:2rem;border:1px solid #D4AF37">
                <h2>Add Product</h2>
                <?php if ($error): ?><p style="color:#e74c3c"><?= htmlspecialchars($error) ?></p><?php endif; ?>
                <form id="addProductForm" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">
                    <div class="form-group"><label>Name</label><input type="text" name="name" required value="<?= htmlspecialchars($formData['name'] ?? '') ?>"></div>
                    <div class="form-group"><label>Category</label>
                        <select name="category_id" required>
                            <?php foreach ($categories as $c): ?><option value="<?= $c['id'] ?>" <?= (isset($formData['category_id']) && $formData['category_id']==$c['id'])?'selected':'' ?>><?= $c['name'] ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group"><label>Description</label><textarea name="description" rows="3"><?= htmlspecialchars($formData['description'] ?? '') ?></textarea></div>
                    <div class="form-group"><label>Price (PKR)</label><input type="number" name="price" step="0.01" required value="<?= htmlspecialchars($formData['price'] ?? '') ?>"></div>
                    <div class="form-group"><label>Sale Price (PKR, optional)</label><input type="number" name="sale_price" step="0.01" value="<?= htmlspecialchars($formData['sale_price'] ?? '') ?>"></div>
                    <div class="form-group"><label>Sizes (comma-separated)</label><input type="text" name="sizes" value="<?= htmlspecialchars($formData['sizes'] ?? 'S,M,L,XL') ?>"></div>
                    <div class="form-group"><label>Colors (comma-separated)</label><input type="text" name="colors" placeholder="Black,Gold,Navy" value="<?= htmlspecialchars($formData['colors'] ?? '') ?>"></div>
                    <div class="form-group"><label>Stock</label><input type="number" name="stock" value="<?= htmlspecialchars($formData['stock'] ?? '100') ?>"></div>
                    <div class="form-group"><label>Images (3–4 images required)</label><input id="addImages" type="file" name="images[]" multiple accept="image/*"></div>
                    <div class="form-group"><label><input type="checkbox" name="featured" <?= !empty($formData['featured']) ? 'checked' : '' ?>> Featured</label></div>
                    <button type="submit" class="btn-gold">Save</button>
                    <button type="button" class="btn-outline" onclick="document.getElementById('productModal').style.display='none'">Cancel</button>
                </form>
            </div>
        </div>
        <script>
        // Client-side check: require 3-4 images for add
        document.getElementById('addProductForm').addEventListener('submit', function(e){
            var inp = document.getElementById('addImages');
            var files = inp.files ? inp.files.length : 0;
            if (files < 3) { alert('Please select at least 3 images.'); e.preventDefault(); return false; }
            if (files > 4) { alert('Please select no more than 4 images.'); e.preventDefault(); return false; }
        });
        // auto-open modal if server-side validation failed
        <?php if ($showAddModal || $error): ?>
        document.getElementById('productModal').style.display = 'block';
        <?php endif; ?>
        </script>
    </main>
</div>
</body>
</html>
