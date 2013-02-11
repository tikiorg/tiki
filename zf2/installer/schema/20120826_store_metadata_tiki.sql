ALTER TABLE `tiki_files` ADD COLUMN `metadata` LONGTEXT AFTER `search_data`;
ALTER TABLE `tiki_file_drafts` ADD COLUMN `metadata` LONGTEXT AFTER `hash`;