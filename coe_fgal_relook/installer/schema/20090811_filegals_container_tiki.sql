-- nyloth - 2009-08-11
INSERT INTO `tiki_file_galleries` (`galleryId`, `name`, `type`, `description`, `visible`, `user`, `public`, `parentId`) VALUES (0, 'File Galleries', 'system', '', 'y', 'admin', 'y', -1);
INSERT INTO `tiki_preferences` (`name`, `value`) VALUES ('fgal_root_id', (SELECT `galleryId` from `tiki_file_galleries` WHERE `type` = 'system') );
UPDATE `tiki_preferences` SET `value` = (SELECT `galleryId` FROM `tiki_file_galleries` WHERE `type` = 'system') WHERE `name` = 'home_file_gallery' AND `value` = '0';
UPDATE `tiki_file_galleries` SET `parentId` = (SELECT * FROM (SELECT `galleryId` FROM `tiki_file_galleries` WHERE `type` = 'system') AS t) WHERE `parentId` = -1 AND `type` != 'system' ;
