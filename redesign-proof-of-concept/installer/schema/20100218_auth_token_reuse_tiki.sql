ALTER TABLE `tiki_auth_tokens` ADD COLUMN `hits` INT NOT NULL DEFAULT 1 AFTER `timeout`;
