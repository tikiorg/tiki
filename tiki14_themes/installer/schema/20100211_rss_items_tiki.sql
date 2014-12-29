
CREATE TABLE `tiki_rss_items` (
	`rssItemId` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`rssId` INT NOT NULL,
	`guid` VARCHAR(255) NOT NULL,
	`url` VARCHAR(255) NOT NULL,
	`publication_date` INT UNSIGNED NOT NULL,
	`title` VARCHAR(255) NOT NULL,
	`author` VARCHAR(255),
	`description` TEXT,
	`content` TEXT,
	KEY `tiki_rss_items_rss` (`rssId`),
	UNIQUE `tiki_rss_items_item` (`rssId`, `guid`)
);

ALTER TABLE `tiki_rss_modules` DROP COLUMN `content`;
ALTER TABLE `tiki_rss_modules` ADD COLUMN `sitetitle` VARCHAR(255);
ALTER TABLE `tiki_rss_modules` ADD COLUMN `siteurl` VARCHAR(255);

