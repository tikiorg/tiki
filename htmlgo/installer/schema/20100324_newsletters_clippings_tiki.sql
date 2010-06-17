ALTER TABLE `tiki_newsletters` ADD COLUMN `allowArticleClip` char(1) default 'y';
ALTER TABLE `tiki_newsletters` ADD COLUMN `autoArticleClip` char(1) default 'n';
ALTER TABLE `tiki_newsletters` ADD COLUMN `articleClipTypes` text;
ALTER TABLE `tiki_newsletters` ADD COLUMN `articleClipRange` int(14) default NULL;
