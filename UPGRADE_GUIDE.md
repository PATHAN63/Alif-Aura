# Alif-Aura Upgrade Integration Guide

## 1. Database Upgrade
```sql
-- Run in phpMyAdmin or MySQL CLI
ALTER TABLE `users` ADD COLUMN `login_attempts` int(11) DEFAULT 0;
ALTER TABLE `users` ADD COLUMN `locked_until` datetime DEFAULT NULL;
ALTER TABLE `users` ADD COLUMN `email_verified` tinyint(1) DEFAULT 0;
ALTER TABLE `users` ADD COLUMN `reset_token` varchar(64) DEFAULT NULL;
ALTER TABLE `users` ADD COLUMN `reset_expires` datetime DEFAULT NULL;
```

## 2. Config
- Update `robots.txt`: Replace `YOURDOMAIN.com` with your domain
- For local dev: Comment out HTTPS redirect in `.htaccess` (lines 1-4)

## 3. SMTP / Order Emails
- Copy `includes/smtp_config.sample.php` to `includes/smtp_config.php`
- Add Gmail SMTP credentials (use App Password for Gmail)
- Optional: `composer require phpmailer/phpmailer` for better email delivery

## 4. PHPMailer (Optional)
```bash
composer require phpmailer/phpmailer
```
Add to `send_order_email.php`:
```php
require __DIR__ . '/../vendor/autoload.php';
```

## 5. Security
- Delete `setup.php` after first run
- Add `includes/smtp_config.php` to `.gitignore`
