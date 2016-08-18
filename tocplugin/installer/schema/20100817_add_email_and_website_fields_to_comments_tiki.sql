ALTER TABLE `tiki_comments` ADD COLUMN `email` varchar(200) DEFAULT NULL AFTER `hash`;
ALTER TABLE `tiki_comments` ADD COLUMN `website` varchar(200) DEFAULT NULL AFTER `email`;
