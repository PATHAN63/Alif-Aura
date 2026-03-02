<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/seo.php';

$id = (int) ($_GET['id'] ?? 0);
if (!$id) {
    header('Location: shop.php');
    exit;
}

$stmt = $pdo->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: shop.php');
    exit;
}

$images = json_decode($product['images'] ?? '[]', true);
if (empty($images)) $images = ['https://images.unsplash.com/photo-1594938298603-c8148c4dae35?w=800&q=90'];
$sizes = $product['sizes'] ? explode(',', $product['sizes']) : ['S', 'M', 'L', 'XL'];
$colors = $product['colors'] ? explode(',', $product['colors']) : [];
$price = $product['sale_price'] ? $product['sale_price'] : $product['price'];
$inWishlist = isLoggedIn() ? inWishlist($pdo, $_SESSION['user_id'], $product['id']) : false;
$seoType = 'product'; $seoId = $product['id'];
$productUrl = SITE_URL . (BASE_PATH ?: '') . '/product.php?id=' . $product['id'] . '&slug=' . urlencode($product['slug']);
$jsonLd = renderJsonLdProduct($product, $productUrl);
$breadcrumb = renderBreadcrumb([
    ['name' => 'Home', 'url' => SITE_URL . (BASE_PATH ?: '') . '/index.php'],
    ['name' => $product['category_name'], 'url' => SITE_URL . (BASE_PATH ?: '') . '/shop.php?cat=' . $product['category_slug']],
    ['name' => $product['name'], 'url' => null]
]);
$extraHead = '<script type="application/ld+json">' . json_encode($jsonLd) . '</script><script type="application/ld+json">' . json_encode($breadcrumb) . '</script>';

require_once 'includes/header.php';
?>

<div class="product-detail section fade-in">
    <div class="product-gallery">
        <div class="main-image-wrap">
            <img src="<?= htmlspecialchars($images[0]) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="main-image" id="main-product-image" loading="eager" onerror="this.src='https://images.unsplash.com/photo-1594938298603-c8148c4dae35?w=800&q=90'">
        </div>
        <div class="product-thumbnails">
            <?php foreach ($images as $i => $img): 
                $thumbUrl = (strpos($img, '?') !== false ? $img . '&w=120' : $img . '?w=120');
            ?>
            <img src="<?= htmlspecialchars($thumbUrl) ?>" alt="View <?= $i+1 ?>" data-full="<?= htmlspecialchars($img) ?>" class="thumb <?= $i === 0 ? 'active' : '' ?>" loading="lazy" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1594938298603-c8148c4dae35?w=120&q=80'">
            <?php endforeach; ?>
        </div>
    </div>
    <div class="product-info">
        <p class="category-badge"><?= htmlspecialchars($product['category_name']) ?></p>
        <h1><?= htmlspecialchars($product['name']) ?></h1>
        <p class="product-price"><?= formatPrice($price) ?>
            <?php if ($product['sale_price']): ?>
                <span class="old"><?= formatPrice($product['price']) ?></span>
            <?php endif; ?>
        </p>
        <p class="product-desc"><?= nl2br(htmlspecialchars($product['description'] ?? 'Elegant modest fashion piece.')) ?></p>
        <form action="cart.php" method="POST" class="product-form">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            <div class="form-group">
                <label>Size</label>
                <select name="size" required>
                    <?php foreach ($sizes as $s): ?>
                    <option value="<?= trim($s) ?>"><?= trim($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if (!empty($colors)): ?>
            <div class="form-group">
                <label>Color</label>
                <select name="color">
                    <?php foreach ($colors as $c): ?>
                    <option value="<?= trim($c) ?>"><?= trim($c) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
            <div class="form-group">
                <label>Quantity</label>
                <input type="number" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>">
            </div>
            <div class="product-actions">
                <button type="submit" class="btn-gold">Add to Cart</button>
                <?php if (isLoggedIn()): ?>
                <button type="button" class="btn-outline wishlist-btn-lg <?= $inWishlist ? 'active' : '' ?>" onclick="wishlistToggle(<?= $product['id'] ?>, this)">Wishlist</button>
                <?php endif; ?>
                <a href="https://wa.me/923001234567?text=Hi!%20Interested%20in%20<?= urlencode($product['name']) ?>" class="btn-outline" target="_blank">Order via WhatsApp</a>
            </div>
        </form>
    </div>
</div>
<script>
function wishlistToggle(id, btn) {
    fetch('api/wishlist.php', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({product_id:id})})
    .then(r=>r.json()).then(d=>{ if(d.success) btn.classList.toggle('active', d.in_wishlist); });
}
document.querySelectorAll('.product-thumbnails img').forEach(t=>{
    t.addEventListener('click', ()=>{ document.getElementById('main-product-image').src = t.dataset.full||t.src;
    document.querySelectorAll('.product-thumbnails img').forEach(x=>x.classList.remove('active')); t.classList.add('active'); });
});
</script>
<?php require_once 'includes/footer.php'; ?>
