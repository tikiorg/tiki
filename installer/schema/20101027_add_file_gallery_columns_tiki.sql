ALTER TABLE `tiki_file_galleries` ADD `show_deleteAfter` CHAR( 1 ) NULL DEFAULT NULL AFTER `show_backlinks`;
ALTER TABLE `tiki_file_galleries` ADD `show_checked` CHAR( 1 ) NULL DEFAULT NULL AFTER `show_deleteAfter`;
ALTER TABLE `tiki_file_galleries` ADD `show_share` CHAR( 1 ) NULL DEFAULT NULL AFTER `show_checked`;
