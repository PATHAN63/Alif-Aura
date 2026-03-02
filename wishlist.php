<?php
$pageTitle = 'Wishlist';
require_once 'includes/header.php';
requireLogin();

$stmt = $pdo->prepare("
    SELECT p.*, w.id as wish_id, c.slug as category_slug 
    FROM wishlist w 
    JOIN products p ON w.product_id = p.id 
    JOIN categories c ON p.category_id = c.id 
    WHERE w.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$items = $stmt->fetchAll();
?>

<div class="section">
    <h1 class="section-title">My Wishlist</h1>
    <?php if (empty($items)): ?>
        <p style="text-align:center;padding:4rem">Your wishlist is empty. <a href="shop.php" class="btn-gold" style="display:inline-block;margin-top:1rem">Browse Collections</a></p>
    <?php else: ?>
    <div class="products-grid">
        <?php foreach ($items as $product): 
            $images = json_decode($product['images'] ?? '[]', true);
            $mainImg = !empty($images) ? $images[0] : 'images/placeholder.jpg';
            $price = $product['sale_price'] ? $product['sale_price'] : $product['price'];
        ?>
        <article class="product-card">
            <a href="product.php?id=<?= $product['id'] ?>&slug=<?= $product['slug'] ?>">
                <div class="image-wrap">
                    <img src="<?= htmlspecialchars($mainImg) ?>" alt="<?= htmlspecialchars($product['name']) ?>" onerror="this.src='https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=400&q=80'">
                </div>
            </a>
            <div class="quick-actions">
                <button class="wishlist-btn active" onclick="removeWishlist(<?= $product['id'] ?>, this.closest('.product-card'))">Remove</button>
            </div>
            <div class="info">
                <h3><a href="product.php?id=<?= $product['id'] ?>&slug=<?= $product['slug'] ?>"><?= htmlspecialchars($product['name']) ?></a></h3>
                <p class="price"><?= formatPrice($price) ?></p>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<script>
function removeWishlist(id, card) {
    fetch('api/wishlist.php', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({product_id:id})})
    .then(r=>r.json()).then(d=>{ if(d.success) card.remove(); });
}
</script>

<?php require_once 'includes/footer.php'; ?>
