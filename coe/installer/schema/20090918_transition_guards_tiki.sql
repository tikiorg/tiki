-- 2009-09-18 lphuberdeau
ALTER TABLE `tiki_transitions` ADD COLUMN guards VARCHAR(1024) NOT NULL DEFAULT '[]';
