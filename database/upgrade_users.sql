-- Add auth upgrade columns to users table
-- Run each ALTER separately; ignore "duplicate column" if exists

ALTER TABLE `users` ADD COLUMN `login_attempts` int(11) DEFAULT 0;
ALTER TABLE `users` ADD COLUMN `locked_until` datetime DEFAULT NULL;
ALTER TABLE `users` ADD COLUMN `email_verified` tinyint(1) DEFAULT 0;
ALTER TABLE `users` ADD COLUMN `reset_token` varchar(64) DEFAULT NULL;
ALTER TABLE `users` ADD COLUMN `reset_expires` datetime DEFAULT NULL;
