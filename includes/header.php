<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/seo.php';

$cartCount = getCartCount();
$wishlistCount = isLoggedIn() ? getWishlistCount($pdo, $_SESSION['user_id']) : 0;
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

$seoType = $seoType ?? 'home';
$seoId = $seoId ?? 0;
$meta = getSeoMeta($pdo, $seoType, $seoId);
$canonicalUrl = SITE_URL . preg_replace('/\?.*/', '', $_SERVER['REQUEST_URI'] ?? '');
$pageTitle = $meta['title'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $pageTitle ?></title>
    <meta name="description" content="<?= $meta['description'] ?>">
    <link rel="canonical" href="<?= $canonicalUrl ?>">
    <meta property="og:type" content="<?= $meta['type'] ?>">
    <meta property="og:title" content="<?= $meta['title'] ?>">
    <meta property="og:description" content="<?= $meta['description'] ?>">
    <meta property="og:url" content="<?= $meta['url'] ?>">
    <meta property="og:image" content="<?= $meta['image'] ?>">
    <meta property="og:site_name" content="<?= SITE_NAME ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= $meta['title'] ?>">
    <meta name="twitter:description" content="<?= $meta['description'] ?>">
    <meta name="twitter:image" content="<?= $meta['image'] ?>">
    <link rel="stylesheet" href="<?= BASE_PATH ?>/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <?= $extraHead ?? '' ?>
</head>
<body>
    <div id="page-loader" class="page-loader">
        <div class="loader-spinner"></div>
        <img src="<?= BASE_PATH ?>/images/logo.png" alt="Alif-Aura" class="loader-logo-img">
    </div>

    <header class="main-header" id="main-header">
        <nav class="nav-container">
            <a href="<?= BASE_PATH ?>/index.php" class="logo">
                <img src="<?= BASE_PATH ?>/images/logo.png" alt="<?= SITE_NAME ?> - <?= SITE_TAGLINE ?>" class="logo-img">
            </a>
            <button class="nav-toggle" id="nav-toggle" aria-label="Toggle menu"><span></span><span></span><span></span></button>
            <ul class="nav-links">
                <li><a href="<?= BASE_PATH ?>/index.php" class="<?= $currentPage === 'index' ? 'active' : '' ?>">Home</a></li>
                <li><a href="<?= BASE_PATH ?>/shop.php?cat=abaya" class="<?= $currentPage === 'shop' && ($_GET['cat'] ?? '') === 'abaya' ? 'active' : '' ?>">Abaya</a></li>
                <li><a href="<?= BASE_PATH ?>/shop.php?cat=kids" class="<?= $currentPage === 'shop' && ($_GET['cat'] ?? '') === 'kids' ? 'active' : '' ?>">Kids</a></li>
                <li><a href="<?= BASE_PATH ?>/about.php" class="<?= $currentPage === 'about' ? 'active' : '' ?>">About</a></li>
                <li><a href="<?= BASE_PATH ?>/blog.php" class="<?= $currentPage === 'blog' ? 'active' : '' ?>">Blog</a></li>
                <li><a href="<?= BASE_PATH ?>/contact.php" class="<?= $currentPage === 'contact' ? 'active' : '' ?>">Contact</a></li>
            </ul>
            <div class="nav-actions">
                <?php if (isLoggedIn()): ?>
                    <a href="<?= BASE_PATH ?>/wishlist.php" class="nav-icon" title="Wishlist">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                        </svg>
                        <?php if ($wishlistCount > 0): ?><span class="badge"><?= $wishlistCount ?></span><?php endif; ?>
                    </a>
                <?php endif; ?>
                <a href="<?= BASE_PATH ?>/cart.php" class="nav-icon" title="Cart">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M9 22a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                        <path d="M20 22a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                    </svg>
                    <?php if ($cartCount > 0): ?><span class="badge"><?= $cartCount ?></span><?php endif; ?>
                </a>
                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?><a href="<?= BASE_PATH ?>/admin/dashboard.php" class="nav-icon" title="Admin">Admin</a><?php endif; ?>
                    <a href="<?= BASE_PATH ?>/logout.php" class="nav-btn">Logout</a>
                <?php else: ?>
                    <a href="<?= BASE_PATH ?>/login.php" class="nav-btn">Login</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    <main class="main-content">
