ALTER TABLE `tiki_calendar_items` CHANGE `description` `description` TEXT NULL DEFAULT NULL ;
ALTER TABLE `tiki_calendar_items` ADD FULLTEXT `ft` ( `name` , `description`
);
