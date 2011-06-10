DROP INDEX `left` ON `tiki_sefurl_regex_out`;
ALTER TABLE `tiki_sefurl_regex_out` ADD UNIQUE `left` (`left`(128));
