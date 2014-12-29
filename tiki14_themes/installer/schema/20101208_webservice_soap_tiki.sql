ALTER TABLE `tiki_webservice` ADD `wstype` CHAR(4) NULL AFTER `url` ;
ALTER TABLE `tiki_webservice` ADD `operation` VARCHAR(250) NULL AFTER `wstype` ;