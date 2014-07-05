ALTER TABLE `tiki_pages` ADD COLUMN `pageSlug` VARCHAR(160) NULL AFTER `pageName`,
                         ADD UNIQUE `pageSlug` (`pageSlug`);
