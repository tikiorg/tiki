ALTER TABLE `tiki_blogs` ADD COLUMN `use_excerpt` char(1) default NULL AFTER `use_author`;
ALTER TABLE `tiki_blog_posts` ADD COLUMN `excerpt` text default NULL AFTER `data_size`;
