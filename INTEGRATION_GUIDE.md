# Alif-Aura Upgrade - Step-by-Step Integration

## 1. Database Upgrade
```sql
-- Run each ALTER in phpMyAdmin or MySQL CLI
ALTER TABLE users ADD COLUMN login_attempts int(11) DEFAULT 0;
ALTER TABLE users ADD COLUMN locked_until datetime DEFAULT NULL;
ALTER TABLE users ADD COLUMN email_verified tinyint(1) DEFAULT 0;
ALTER TABLE users ADD COLUMN reset_token varchar(64) DEFAULT NULL;
ALTER TABLE users ADD COLUMN reset_expires datetime DEFAULT NULL;
```

## 2. robots.txt
- Edit `robots.txt`: Replace `YOURDOMAIN.com` with your domain
- Sitemap URL: `https://yourdomain.com/sitemap.php`

## 3. Local Development
- Comment out lines 1-5 in `.htaccess` (HTTPS redirect) if testing on localhost

## 4. SMTP (Order Emails)
- Copy `includes/smtp_config.sample.php` to `includes/smtp_config.php`
- Fill Gmail SMTP: host, username, password (App Password)
- Optional: `composer require phpmailer/phpmailer` for reliable delivery

## 5. Subdirectory Install
- If site is in `/alif-aura/`, BASE_PATH auto-detects from SCRIPT_NAME
- Update `robots.txt` Sitemap URL accordingly

## Files Modified
- includes/config.php, functions.php, header.php
- includes/seo.php (new)
- includes/auth.php (new)
- includes/send_order_email.php (new)
- login.php, register.php, checkout.php, product.php
- forgot_password.php, reset_password.php (new)
- .htaccess, admin/.htaccess
- sitemap.php, robots.txt
- database/upgrade_users.sql, database/indexes.sql
