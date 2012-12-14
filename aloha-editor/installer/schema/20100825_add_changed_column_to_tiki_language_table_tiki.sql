ALTER TABLE `tiki_language` DROP PRIMARY KEY;
ALTER TABLE `tiki_language` ADD COLUMN `id` int(14) NOT NULL PRIMARY KEY auto_increment FIRST;
ALTER TABLE `tiki_language` ADD COLUMN `changed` bool DEFAULT 0;
