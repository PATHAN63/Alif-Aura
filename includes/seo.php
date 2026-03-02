<?php
function getSeoMeta($pdo, $type = 'home', $id = 0) {
    $reqUri = $_SERVER['REQUEST_URI'] ?? '';
$meta = [
        'title' => SITE_NAME . ' - ' . SITE_TAGLINE,
        'description' => SITE_DESC,
        'image' => SITE_URL . (defined('BASE_PATH') && BASE_PATH ? BASE_PATH : '') . '/images/logo.png',
        'url' => SITE_URL . $reqUri,
        'type' => 'website'
    ];
    if ($type === 'product' && $id) {
        $stmt = $pdo->prepare("SELECT p.name, p.description, p.price, p.sale_price, p.images, c.name as cat FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
        $stmt->execute([$id]);
        $p = $stmt->fetch();
        if ($p) {
            $imgs = json_decode($p['images'] ?? '[]', true);
            $meta['title'] = sanitize($p['name']) . ' | ' . SITE_NAME;
            $meta['description'] = sanitize(mb_substr(strip_tags($p['description'] ?? $p['name']), 0, 160));
            $meta['image'] = !empty($imgs[0]) ? (strpos($imgs[0], 'http') === 0 ? $imgs[0] : SITE_URL . (defined('BASE_PATH') ? BASE_PATH : '') . '/' . $imgs[0]) : $meta['image'];
            $meta['type'] = 'product';
        }
    } elseif ($type === 'page') {
        $titles = ['about' => 'About Us', 'contact' => 'Contact', 'shop' => 'Shop', 'blog' => 'Blog', 'cart' => 'Cart', 'checkout' => 'Checkout', 'login' => 'Login', 'register' => 'Register'];
        $page = basename($_SERVER['PHP_SELF'], '.php');
        if (isset($titles[$page])) $meta['title'] = $titles[$page] . ' | ' . SITE_NAME;
    }
    return $meta;
}

function renderJsonLdProduct($product, $fullUrl) {
    $price = $product['sale_price'] ?: $product['price'];
    $imgs = json_decode($product['images'] ?? '[]', true);
    $base = defined('BASE_PATH') ? BASE_PATH : '';
$img = !empty($imgs[0]) ? (strpos($imgs[0], 'http') === 0 ? $imgs[0] : SITE_URL . $base . '/' . $imgs[0]) : SITE_URL . $base . '/images/logo.png';
    return [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $product['name'],
        'description' => $product['description'] ?? $product['name'],
        'image' => $img,
        'url' => $fullUrl,
        'brand' => ['@type' => 'Brand', 'name' => SITE_NAME],
        'offers' => [
            '@type' => 'Offer',
            'price' => $price,
            'priceCurrency' => CURRENCY_CODE
        ]
    ];
}

function renderBreadcrumb($items) {
    $list = [];
    foreach ($items as $i => $item) {
        $list[] = [
            '@type' => 'ListItem',
            'position' => $i + 1,
            'name' => $item['name'],
            'item' => $item['url'] ?? null
        ];
    }
    return ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $list];
}
