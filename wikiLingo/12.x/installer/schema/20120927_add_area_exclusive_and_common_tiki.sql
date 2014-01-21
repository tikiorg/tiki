ALTER IGNORE TABLE `tiki_areas` ADD `exclusive` CHAR(1) NOT NULL  DEFAULT 'n';
ALTER IGNORE TABLE `tiki_areas` ADD `share_common` CHAR(1)  NOT NULL  DEFAULT 'y';
ALTER IGNORE TABLE `tiki_areas` ADD `enabled` CHAR(1)  NOT NULL  DEFAULT 'y';
