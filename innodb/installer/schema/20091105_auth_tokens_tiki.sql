-- 2009-11-05 lphuberdeau
CREATE TABLE `tiki_auth_tokens` (
	`tokenId` INT NOT NULL AUTO_INCREMENT,
	`creation` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`timeout` INT NOT NULL DEFAULT 0,
	`token` CHAR(32),
	`entry` VARCHAR(50),
	`parameters` VARCHAR(255),
	`groups` VARCHAR(255),
	PRIMARY KEY( `tokenId` ),
	KEY `tiki_auth_tokens_token` (`token`)
);
