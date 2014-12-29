ALTER TABLE `tiki_payment_received` ADD COLUMN `status` VARCHAR(15) NOT NULL DEFAULT 'paid' AFTER `type`;
ALTER TABLE `tiki_payment_requests` ADD COLUMN `authorized_until` TIMESTAMP NULL AFTER `due_date`;
