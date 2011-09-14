CREATE TABLE `tiki_discount`( 
	`id` INT NOT NULL AUTO_INCREMENT,
	`code` VARCHAR(255),
	`value` VARCHAR(255),
	`max` INT,
	`comment` TEXT,
	PRIMARY KEY(`id`),
	KEY `code` (`code`)
);
