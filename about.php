<?php
$pageTitle = 'About';
require_once 'includes/header.php';
?>

<section class="section fade-in about-hero">
    <div class="about-header">
        <img src="images/logo.png" alt="Alif-Aura" class="about-logo">
        <h1>Our Story</h1>
        <p class="about-tagline"><?= SITE_TAGLINE ?></p>
        <p class="about-sub">LUXURY • ELEGANCE • PRESTIGE</p>
    </div>
</section>

<section class="section fade-in about-content">
    <div class="about-grid">
        <div class="about-image">
            <div class="about-image-grid">
                <img src="images/Elegance_Made_Easy_Desktop__jpg.webp" alt="Abaya Collection" />
                <img src="images/Memories_1880x720__jpg.webp" alt="Kids Collection" />
            </div>
        </div>
        <div class="about-text">
            <h2>Luxury Modest Fashion</h2>
            <p>Alif-Aura was born from a vision to bring luxury modest fashion to women who seek elegance without compromise. Every piece we create reflects our commitment to quality, sophistication, and timeless style.</p>
            <p>Our Abaya and Kids collections are crafted with the finest materials and attention to detail. From the sewing needle to the final stitch, we ensure that every garment you wear becomes a statement of refined grace.</p>
        </div>
    </div>
</section>

<section class="section fade-in about-section-alt">
    <div class="about-grid about-grid-reverse">
        <div class="about-text">
            <h2>Our Mission</h2>
            <p>We believe that modesty and luxury can coexist beautifully. Our mission is to create garments that make every woman feel confident, elegant, and dignified—whether at a formal event or in everyday life.</p>
        </div>
        <div class="about-image">
            <img src="images/Together_in_Faith_Desktop__jpg.webp" alt="Craftsmanship & Design">
        </div>
    </div>
</section>

<section class="section fade-in about-mission">
    <h2 class="section-title">Our Values</h2>
    <div class="values-grid">
        <div class="value-card">
            <div class="value-icon">◆</div>
            <h3>Quality First</h3>
            <p>Premium fabrics and craftsmanship in every stitch. We source the finest materials to ensure durability and comfort.</p>
        </div>
        <div class="value-card">
            <div class="value-icon">◆</div>
            <h3>Timeless Design</h3>
            <p>Elegant styles that transcend trends. Our designs are classic yet contemporary, perfect for any occasion.</p>
        </div>
        <div class="value-card">
            <div class="value-icon">◆</div>
            <h3>Modest Luxury</h3>
            <p>Where modesty meets sophistication. We believe modest wear can be both stylish and luxurious.</p>
        </div>
    </div>
</section>

<section class="section fade-in about-stats">
    <div class="stats-row">
        <div class="stat-item"><span class="stat-num">500+</span><span>Happy Customers</span></div>
        <div class="stat-item"><span class="stat-num">2</span><span>Collections</span></div>
        <div class="stat-item"><span class="stat-num">10+</span><span>Unique Designs</span></div>
    </div>
</section>

<section class="section fade-in about-cta">
    <h2>Ready to Explore?</h2>
    <p>Discover our exclusive Abaya and Kids collections.</p>
    <a href="shop.php" class="btn-gold">Shop Now</a>
</section>

<?php require_once 'includes/footer.php'; ?>
