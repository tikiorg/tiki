CREATE TABLE `tiki_search_queries` (
	`queryId` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`userId` INT NOT NULL,
	`lastModif` INT,
	`label` VARCHAR(100) NOT NULL,
	`priority` VARCHAR(15) NOT NULL,
	`query` BLOB,
	INDEX `query_userId` (`userId`)
);
