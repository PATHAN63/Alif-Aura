<?php
if (session_status() === PHP_SESSION_NONE) session_start();

define('SITE_NAME', 'Alif-Aura');
define('SITE_TAGLINE', 'Elegance in Every Layer');
define('SITE_DESC', 'Alif-Aura - Luxury modest fashion brand. Shop elegant Abaya & Kids collections. Premium quality, timeless designs.');
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '/');
$basePath = ($scriptDir === '/' || $scriptDir === '\\') ? '' : rtrim($scriptDir, '/\\');
define('SITE_URL', $protocol . '://' . $host);
define('BASE_PATH', $basePath);
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('CURRENCY', '₨');
define('CURRENCY_CODE', 'PKR');
define('RECAPTCHA_SITE_KEY', ''); // Add your key
define('RECAPTCHA_SECRET_KEY', ''); // Add your secret
