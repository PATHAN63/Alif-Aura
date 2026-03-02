<?php
/**
 * Rename to smtp_config.php and fill credentials
 */
return [
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'encryption' => 'tls',
    'username' => 'your-email@gmail.com',
    'password' => 'your-app-password',
    'from_email' => 'your-email@gmail.com',
    'from_name' => SITE_NAME,
];
