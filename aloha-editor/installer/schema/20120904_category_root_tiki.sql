ALTER TABLE `tiki_categories` ADD COLUMN `rootId` INT NOT NULL DEFAULT 0 AFTER `parentId`;
