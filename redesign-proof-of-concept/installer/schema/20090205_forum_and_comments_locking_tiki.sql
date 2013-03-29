ALTER TABLE `tiki_comments` ADD COLUMN `locked` char(1) NOT NULL default 'n';
ALTER TABLE `tiki_objects` ADD COLUMN `comments_locked` char(1) NOT NULL default 'n';
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`) VALUES ('tiki_p_forum_lock', 'Can lock forums and threads', 'editors', 'forums');
INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`) VALUES ('tiki_p_lock_comments', 'Can lock comments', 'editors', 'comments');
UPDATE `tiki_comments` SET `locked` = 'y' WHERE `type` = 'l';
UPDATE `tiki_comments` SET `type` = 'n' WHERE `type` = 'l';
