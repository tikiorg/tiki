CREATE TABLE IF NOT EXISTS `tiki_newsletter_pages` (
	`nlId` INT( 12 ) NOT NULL ,
	`wikiPageName` VARCHAR( 160 ) NOT NULL ,
	`validateAddrs` CHAR( 1 ) NOT NULL DEFAULT 'n',
	`addToList` CHAR( 1 ) NOT NULL DEFAULT 'n',
	PRIMARY KEY ( `nlId` , `wikiPageName` )
) ENGINE = MYISAM ;
