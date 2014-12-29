
CREATE TABLE `tiki_goals` (
	`goalId` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`name` VARCHAR(50) NOT NULL,
	`type` VARCHAR(10) NOT NULL DEFAULT 'user',
	`description` TEXT,
	`enabled` INT NOT NULL DEFAULT 0,
	`daySpan` INT NOT NULL DEFAULT 14,
	`from` DATETIME,
	`to` DATETIME,
	`eligible` BLOB,
	`conditions` BLOB,
	`rewards` BLOB
) ENGINE=MyISAM;

