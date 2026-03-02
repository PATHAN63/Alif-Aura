<?php
/**
 * Run: php database/migrate.php
 * Adds missing columns and indexes to existing database
 */
require_once __DIR__ . '/../includes/db.php';

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$alterColumns = [
    "ALTER TABLE users ADD COLUMN login_attempts int(11) DEFAULT 0",
    "ALTER TABLE users ADD COLUMN locked_until datetime DEFAULT NULL",
    "ALTER TABLE users ADD COLUMN email_verified tinyint(1) DEFAULT 0",
    "ALTER TABLE users ADD COLUMN reset_token varchar(64) DEFAULT NULL",
    "ALTER TABLE users ADD COLUMN reset_expires datetime DEFAULT NULL",
];

$indexes = [
    "ALTER TABLE products ADD INDEX idx_products_slug (slug)",
    "ALTER TABLE products ADD INDEX idx_products_featured_created (featured, created_at)",
    "ALTER TABLE orders ADD INDEX idx_orders_created (created_at)",
    "ALTER TABLE wishlist ADD INDEX idx_wishlist_user (user_id)",
];

echo "Migrating database...\n";

foreach ($alterColumns as $sql) {
    try {
        $pdo->exec($sql);
        echo "OK: " . substr($sql, 0, 50) . "...\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "Skip (exists): " . substr($sql, 0, 40) . "\n";
        } else throw $e;
    }
}

foreach ($indexes as $sql) {
    try {
        $pdo->exec($sql);
        echo "OK: Index added\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate key') !== false) {
            echo "Skip (exists): Index\n";
        } else throw $e;
    }
}

echo "Migration complete.\n";
