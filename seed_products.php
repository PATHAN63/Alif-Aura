<?php
/**
 * Seed 10 products for Alif-Aura (5 Abaya + 5 Kids)
 * Run: php seed_products.php
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

// Unique image sets per product (5-8 images each, high quality for detail page)
$abayaImgSets = [
    ['images/Elegance_Made_Easy_Desktop__jpg.webp', 'uploads/products/69a563a6cef05.avif', 'uploads/products/69a563a6cf4cc.avif'],
    ['uploads/products/69a563a6cf9c4.avif', 'uploads/products/69a4c5f8304f6.webp', 'images/Together_in_Faith_Desktop__jpg.webp'],
    ['images/Memories_1880x720__jpg.webp', 'uploads/products/69a563a6cef05.avif', 'uploads/products/69a4c584ab2f5.webp'],
    ['uploads/products/69a4c5f830783.webp', 'uploads/products/69a563a6cf9c4.avif', 'images/Elegance_Made_Easy_Desktop__jpg.webp'],
    ['uploads/products/69a4c39a9b3e4.webp', 'uploads/products/69a4c5f830a11.webp', 'images/Memories_1880x720__jpg.webp'],
];
$kidsImgSets = [
    ['uploads/products/69a4c572d9b55.JPG', 'uploads/products/69a4c584ab2f5.webp', 'images/Memories_1880x720__jpg.webp'],
    ['uploads/products/69a4c5f8304f6.webp', 'uploads/products/69a4c5f830783.webp', 'uploads/products/69a4c5f830a11.webp'],
    ['images/Together_in_Faith_Desktop__jpg.webp', 'uploads/products/69a563a6cef05.avif', 'uploads/products/69a563a6cf4cc.avif'],
    ['uploads/products/69a563a6cf9c4.avif', 'images/Elegance_Made_Easy_Desktop__jpg.webp', 'uploads/products/69a4c39a9b3e4.webp'],
    ['uploads/products/69a4c572d9b55.JPG', 'images/Memories_1880x720__jpg.webp', 'uploads/products/69a4c584ab2f5.webp'],
];

$abayaProducts = [
    ['Elegant Black Abaya', 'Premium black abaya with gold trim and flowing silhouette. Crafted from luxurious crepe fabric, perfect for special occasions. Features intricate embroidery detail and comfortable fit.', 8500, 7500, 'S,M,L,XL', 'Black,Gold'],
    ['Royal Navy Abaya', 'Navy blue modest abaya with elegant design. Made from premium jersey fabric for everyday comfort. Perfect blend of tradition and contemporary style.', 9200, null, 'S,M,L,XL', 'Navy'],
    ['Classic Burgundy Abaya', 'Rich burgundy abaya with subtle sheen. Timeless design with flattering cut. Ideal for weddings and formal events.', 7800, null, 'S,M,L,XL', 'Burgundy'],
    ['Golden Embroidery Abaya', 'Stunning black abaya with golden embroidery accents. Hand-finished details. Statement piece for those who appreciate luxury.', 12000, 10800, 'M,L,XL', 'Black'],
    ['Simple Cream Abaya', 'Minimal cream abaya for everyday elegance. Lightweight chiffon fabric. Versatile design pairs with any occasion.', 4500, null, 'S,M,L,XL', 'Cream'],
];

$kidsProducts = [
    ['Kids Pink Abaya', 'Adorable pink abaya for girls. Soft cotton blend, comfortable for all-day wear. Perfect for Eid and family gatherings.', 2500, null, '2-4,4-6,6-8', 'Pink'],
    ['Boys White Thobe', 'Classic white thobe for boys. Breathable fabric, easy care. Traditional design with modern comfort.', 1800, null, '2-4,4-6,6-8', 'White'],
    ['Kids Navy Modest Set', 'Navy modest set for kids. Coordinated top and bottom. Durable and stylish.', 3200, 2800, '4-6,6-8', 'Navy'],
    ['Girls Floral Abaya', 'Floral print kids abaya. Delicate patterns, gentle on skin. Adorable for little fashionistas.', 3500, null, '2-4,4-6,6-8', 'Multicolor'],
    ['Kids Black Abaya', 'Simple black kids abaya. Essential wardrobe piece. Easy to layer and style.', 1500, null, '2-4,4-6,6-8', 'Black'],
];

// Clear existing products (optional - comment out to keep existing)
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
$pdo->exec("TRUNCATE TABLE order_items");
$pdo->exec("TRUNCATE TABLE wishlist");
$pdo->exec("TRUNCATE TABLE products");
$pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

$catRows = $pdo->query("SELECT id, slug FROM categories")->fetchAll();
$categories = [];
foreach ($catRows as $r) $categories[$r['slug']] = $r['id'];

$stmt = $pdo->prepare("INSERT INTO products (name, slug, category_id, description, price, sale_price, sizes, colors, stock, featured, images) VALUES (?,?,?,?,?,?,?,?,?,?,?)");

foreach ($abayaProducts as $i => $p) {
    $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower($p[0]));
    $imgs = $abayaImgSets[$i % count($abayaImgSets)];
    $imgJson = json_encode($imgs);
    $stmt->execute([
        $p[0], $slug, $categories['abaya'] ?? 1, $p[1], (float)$p[2], $p[3] ? (float)$p[3] : null, $p[4], $p[5], 100, 1, $imgJson
    ]);
}
foreach ($kidsProducts as $i => $p) {
    $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower($p[0]));
    $imgs = $kidsImgSets[$i % count($kidsImgSets)];
    $imgJson = json_encode($imgs);
    $stmt->execute([
        $p[0], $slug, $categories['kids'] ?? 2, $p[1], (float)$p[2], $p[3] ? (float)$p[3] : null, $p[4], $p[5], 100, 1, $imgJson
    ]);
}

echo "10 products seeded successfully (5 Abaya + 5 Kids).\n";
