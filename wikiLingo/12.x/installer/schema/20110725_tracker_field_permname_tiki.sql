ALTER TABLE `tiki_tracker_fields` ADD `permName` VARCHAR( 100 ) NULL AFTER `name`;
ALTER TABLE `tiki_tracker_fields` ADD UNIQUE `permName` ( `permName` , `trackerId` );
