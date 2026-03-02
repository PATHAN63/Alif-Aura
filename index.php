<?php
$pageTitle = 'Home';
require_once 'includes/header.php';

// Fetch featured products
$stmt = $pdo->query("
    SELECT p.*, c.name as category_name, c.slug as category_slug 
    FROM products p 
    JOIN categories c ON p.category_id = c.id 
    WHERE p.featured = 1 
    ORDER BY p.created_at DESC 
    LIMIT 8
");
$featuredProducts = $stmt->fetchAll();
?>

<!-- Hero Carousel -->
<section class="hero-carousel">
    <div class="carousel-slides">
        <div class="carousel-slide active" style="background-image:url('https://blackcamels.com.pk/cdn/shop/files/Together_in_Faith_Desktop__jpg.jpg?v=1770707907&width=1370')">
            <div class="carousel-overlay"></div>
            <div class="carousel-content">
                <h2>Abaya Collection</h2>
                <p>Elegant modest wear for the modern woman</p>
                <a href="shop.php?cat=abaya" class="btn-gold">Shop Now</a>
            </div>
        </div>
        <div class="carousel-slide" style="background-image:url('https://shopkjsboutique.com/cdn/shop/files/2.jpg?v=1737046709&width=3840')">
            <div class="carousel-overlay"></div>
            <div class="carousel-content">
                <h2>Kids Collection</h2>
                <p>Adorable modest fashion for little ones</p>
                <a href="shop.php?cat=kids" class="btn-gold">Shop Now</a>
            </div>
        </div>
        <div class="carousel-slide" style="background-image:url('https://maisonestrellas.com/cdn/shop/articles/freepik__35mm-film-photography-a-dark-royal-ramadan-luxury-__10270_2be50d94-f5ab-4882-b8d1-f6737c7538b1.png?v=1769157577&width=1200')">
            <div class="carousel-overlay"></div>
            <div class="carousel-content">
                <h2>LUXURY • ELEGANCE • PRESTIGE</h2>
                <p><?= SITE_TAGLINE ?></p>
                <a href="shop.php" class="btn-gold">Explore All</a>
            </div>
        </div>
    </div>
    <button class="carousel-btn prev" aria-label="Previous">‹</button>
    <button class="carousel-btn next" aria-label="Next">›</button>
    <div class="carousel-dots"></div>
</section>

<!-- Collections Preview -->
<section class="section fade-in">
    <h2 class="section-title">Our Collections</h2>
    <div class="collections-grid">
        <a href="shop.php?cat=abaya" class="collection-card">
            <img src="images/collections/abaya-banner.jpg" alt="Abaya Collection" onerror="this.src='https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=800&q=80'">
            <div class="content">
                <h3>Abaya Collection</h3>
                <p>Elegant modest wear for the modern woman</p>
                <span class="btn-outline">Explore</span>
            </div>
        </a>
        <a href="shop.php?cat=kids" class="collection-card">
            <img src="images/collections/kids-banner.jpg" alt="Kids Collection" onerror="this.src='https://images.unsplash.com/photo-1503919545889-aef636e10ad4?w=800&q=80'">
            <div class="content">
                <h3>Kids Collection</h3>
                <p>Adorable modest fashion for little ones</p>
                <span class="btn-outline">Explore</span>
            </div>
        </a>
    </div>
</section>

<!-- Featured / Best Seller Products -->
<section class="section fade-in">
    <h2 class="section-title">Best Sellers</h2>
    <div class="products-grid">
        <?php 
        if (empty($featuredProducts)) {
            $stmt = $pdo->query("SELECT p.*, c.slug as category_slug FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC LIMIT 10");
            $featuredProducts = $stmt->fetchAll();
        }
        ?>
        <?php foreach ($featuredProducts as $product): 
            $images = json_decode($product['images'] ?? '[]', true);
            $mainImg = !empty($images) ? $images[0] : 'images/placeholder.jpg';
            $price = $product['sale_price'] ? $product['sale_price'] : $product['price'];
            $inWishlist = isLoggedIn() ? inWishlist($pdo, $_SESSION['user_id'], $product['id']) : false;
        ?>
        <article class="product-card">
            <a href="product.php?id=<?= $product['id'] ?>&slug=<?= $product['slug'] ?>">
                <div class="image-wrap">
                    <img src="<?= htmlspecialchars($mainImg) ?>" alt="<?= generateAlt($product['name'], $product['category_name']) ?>" loading="lazy" onerror="this.src='https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=400&q=80'">
                </div>
            </a>
            <div class="quick-actions">
                <?php if (isLoggedIn()): ?>
                <button class="wishlist-btn <?= $inWishlist ? 'active' : '' ?>" onclick="event.preventDefault(); wishlistToggle(<?= $product['id'] ?>, this)" title="Wishlist">
                    <svg viewBox="0 0 24 24" fill="<?= $inWishlist ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="1.5" width="18" height="18">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                    </svg>
                </button>
                <?php endif; ?>
                <a href="product.php?id=<?= $product['id'] ?>&slug=<?= $product['slug'] ?>" title="View">View</a>
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
        <?php if (empty($featuredProducts)): ?>
            <p style="grid-column:1/-1;text-align:center;padding:2rem;color:#8a8a8a">No products yet. Run <code>php seed_products.php</code> to add 10 sample products.</p>
        <?php endif; ?>
    </div>
    <div style="text-align:center;margin-top:2rem">
        <a href="shop.php" class="btn-gold">View All Products</a>
    </div>
</section>

<!-- Customer Reviews -->
<section class="section fade-in reviews-section">
    <h2 class="section-title">What Our Customers Say</h2>
    <div class="reviews-grid">
        <div class="review-card">
            <div class="stars">★★★★★</div>
            <p>"Absolutely stunning quality! The abaya I purchased exceeded my expectations. The fabric is luxurious and the fit is perfect. Alif-Aura never disappoints."</p>
            <p class="review-author"><strong>Ayesha Khan</strong> — Karachi</p>
        </div>
        <div class="review-card">
            <div class="stars">★★★★★</div>
            <p>"Ordered the Kids Pink Abaya for my daughter's Eid. She looked adorable! Fast delivery and excellent packaging. Will definitely order again."</p>
            <p class="review-author"><strong>Fatima Ahmed</strong> — Lahore</p>
        </div>
        <div class="review-card">
            <div class="stars">★★★★★</div>
            <p>"The Golden Embroidery Abaya is a masterpiece. Received so many compliments. Worth every rupee. Luxury at its finest."</p>
            <p class="review-author"><strong>Zainab Malik</strong> — Islamabad</p>
        </div>
        <div class="review-card">
            <div class="stars">★★★★★</div>
            <p>"Best modest fashion brand in Pakistan. My go-to for all occasions. The kids collection is especially charming."</p>
            <p class="review-author"><strong>Sana Hussain</strong> — Rawalpindi</p>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
