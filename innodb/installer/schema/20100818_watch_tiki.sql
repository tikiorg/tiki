ALTER TABLE `tiki_user_watches` DROP PRIMARY KEY , ADD PRIMARY KEY ( `watchId` );
ALTER TABLE `tiki_user_watches` DROP INDEX `watchId`;
ALTER TABLE `tiki_group_watches` DROP PRIMARY KEY , ADD PRIMARY KEY ( `watchId` );
ALTER TABLE `tiki_group_watches` DROP INDEX `watchId`;
ALTER TABLE `tiki_user_watches` CHANGE `email` `email` VARCHAR( 200 ) NULL DEFAULT NULL;
ALTER TABLE `tiki_user_watches` ADD INDEX `event-object-user` ( `event` , `object` ( 100 ) , `user` ( 50 ) );
ALTER TABLE `tiki_group_watches` ADD INDEX `event-object-group` ( `event` , `object` ( 100 ) , `group` ( 50 ) );
