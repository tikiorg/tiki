CREATE TABLE IF NOT EXISTS `tiki_tabular_formats` (
	`tabularId` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`trackerId` INT NOT NULL,
	`name` VARCHAR(30) NOT NULL,
	`format_descriptor` TEXT,
	KEY `tabular_tracker_ix` (`trackerId`)
);
