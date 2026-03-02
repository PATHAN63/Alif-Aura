<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

header('Content-Type: application/xml; charset=utf-8');
$base = SITE_URL . (BASE_PATH ?: '');

$urls = [
    ['loc' => $base . '/index.php', 'priority' => '1.0', 'changefreq' => 'daily'],
    ['loc' => $base . '/shop.php', 'priority' => '0.9', 'changefreq' => 'daily'],
    ['loc' => $base . '/shop.php?cat=abaya', 'priority' => '0.9', 'changefreq' => 'daily'],
    ['loc' => $base . '/shop.php?cat=kids', 'priority' => '0.9', 'changefreq' => 'daily'],
    ['loc' => $base . '/about.php', 'priority' => '0.8', 'changefreq' => 'monthly'],
    ['loc' => $base . '/contact.php', 'priority' => '0.8', 'changefreq' => 'monthly'],
    ['loc' => $base . '/blog.php', 'priority' => '0.7', 'changefreq' => 'weekly'],
];

$products = $pdo->query("SELECT id, slug, updated_at FROM products")->fetchAll(PDO::FETCH_ASSOC);
foreach ($products as $p) {
    $urls[] = [
        'loc' => $base . '/product.php?id=' . $p['id'] . '&slug=' . urlencode($p['slug']),
        'priority' => '0.8',
        'changefreq' => 'weekly',
        'lastmod' => date('Y-m-d', strtotime($p['updated_at'] ?? 'now'))
    ];
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as $u) {
    echo '<url>';
    echo '<loc>' . htmlspecialchars($u['loc']) . '</loc>';
    if (!empty($u['lastmod'])) echo '<lastmod>' . $u['lastmod'] . '</lastmod>';
    echo '<changefreq>' . ($u['changefreq'] ?? 'weekly') . '</changefreq>';
    echo '<priority>' . ($u['priority'] ?? '0.5') . '</priority>';
    echo '</url>' . "\n";
}
echo '</urlset>';
