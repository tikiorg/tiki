
CREATE TABLE `tiki_source_auth` (
	`identifier` VARCHAR(50) PRIMARY KEY,
	`scheme` VARCHAR(20) NOT NULL,
	`domain` VARCHAR(200) NOT NULL,
	`path` VARCHAR(200) NOT NULL,
	`method` VARCHAR(20) NOT NULL,
	`arguments` TEXT NOT NULL,
	KEY `tiki_source_auth_ix` (`scheme`, `domain`)
);

