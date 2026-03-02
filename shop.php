<?php
$pageTitle = 'Shop';
require_once 'includes/header.php';

$cat = sanitize($_GET['cat'] ?? '');
$sort = sanitize($_GET['sort'] ?? 'newest');
$color = sanitize($_GET['color'] ?? '');
$minPrice = (float) ($_GET['min_price'] ?? 0);
$maxPrice = (float) ($_GET['max_price'] ?? 0);

$sql = "SELECT p.*, c.name as category_name, c.slug as category_slug FROM products p JOIN categories c ON p.category_id = c.id WHERE 1=1";
$params = [];

if ($cat) {
    $sql .= " AND c.slug = ?";
    $params[] = $cat;
}
if ($minPrice > 0) {
    $sql .= " AND (COALESCE(p.sale_price, p.price)) >= ?";
    $params[] = $minPrice;
}
if ($maxPrice > 0) {
    $sql .= " AND (COALESCE(p.sale_price, p.price)) <= ?";
    $params[] = $maxPrice;
}

switch ($sort) {
    case 'price_low': $sql .= " ORDER BY (COALESCE(p.sale_price, p.price)) ASC"; break;
    case 'price_high': $sql .= " ORDER BY (COALESCE(p.sale_price, p.price)) DESC"; break;
    default: $sql .= " ORDER BY p.created_at DESC";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<div class="shop-layout">
    <aside class="filters-sidebar">
        <form method="GET" class="filters-form">
            <?php if ($cat): ?><input type="hidden" name="cat" value="<?= $cat ?>"><?php endif; ?>
            <div class="filter-group">
                <h4>Category</h4>
                <a href="shop.php" class="filter-option <?= !$cat ? 'active' : '' ?>">All</a>
                <a href="shop.php?cat=abaya" class="filter-option <?= $cat === 'abaya' ? 'active' : '' ?>">Abaya</a>
                <a href="shop.php?cat=kids" class="filter-option <?= $cat === 'kids' ? 'active' : '' ?>">Kids</a>
            </div>
            <div class="filter-group">
                <h4>Price Range (PKR)</h4>
                <input type="number" name="min_price" placeholder="Min" value="<?= $minPrice ?: '' ?>" class="filter-input" style="margin-bottom:0.5rem">
                <input type="number" name="max_price" placeholder="Max" value="<?= $maxPrice ?: '' ?>" class="filter-input">
            </div>
            <div class="filter-group">
                <h4>Sort By</h4>
                <select name="sort" class="filter-input" onchange="this.form.submit()">
                    <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest</option>
                    <option value="price_low" <?= $sort === 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
                    <option value="price_high" <?= $sort === 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
                </select>
            </div>
            <button type="submit" class="btn-gold" style="width:100%;margin-top:1rem">Apply Filters</button>
        </form>
    </aside>
    
    <div class="shop-products">
        <h1 class="section-title" style="text-align:left">
            <?= $cat ? ucfirst($cat) . ' Collection' : 'All Products' ?>
        </h1>
        <div class="products-grid">
            <?php foreach ($products as $product): 
                $images = json_decode($product['images'] ?? '[]', true);
                $mainImg = !empty($images) ? $images[0] : 'images/placeholder.jpg';
                $price = $product['sale_price'] ? $product['sale_price'] : $product['price'];
                $inWishlist = isLoggedIn() ? inWishlist($pdo, $_SESSION['user_id'], $product['id']) : false;
            ?>
            <article class="product-card">
                <a href="product.php?id=<?= $product['id'] ?>&slug=<?= $product['slug'] ?>">
                    <div class="image-wrap">
                        <img src="<?= htmlspecialchars($mainImg) ?>" alt="<?= generateAlt($product['name'], $product['category_name'] ?? '') ?>" loading="lazy" onerror="this.src='https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=400&q=80'">
                    </div>
                </a>
                <div class="quick-actions">
                    <?php if (isLoggedIn()): ?>
                    <button class="wishlist-btn <?= $inWishlist ? 'active' : '' ?>" onclick="event.preventDefault(); wishlistToggle(<?= $product['id'] ?>, this)">
                        <svg viewBox="0 0 24 24" fill="<?= $inWishlist ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="1.5" width="18" height="18">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                        </svg>
                    </button>
                    <?php endif; ?>
                    <a href="product.php?id=<?= $product['id'] ?>&slug=<?= $product['slug'] ?>" class="btn-outline" style="padding:0.5rem 1rem;font-size:0.8rem">Add to Cart</a>
                </div>
                <div class="info">
                    <h3><a href="product.php?id=<?= $product['id'] ?>&slug=<?= $product['slug'] ?>"><?= htmlspecialchars($product['name']) ?></a></h3>
                    <p class="price">
                        <?php if ($product['sale_price']): ?>
                            <span class="old"><?= formatPrice($product['price']) ?></span>
                        <?php endif; ?>
                        <?= formatPrice($price) ?>
                    </p>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php if (empty($products)): ?>
            <p style="text-align:center;padding:4rem">No products found. Add products from the admin panel.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
