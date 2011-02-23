CREATE TABLE `tiki_queue` (
	`entryId` INT PRIMARY KEY AUTO_INCREMENT,
	`queue` VARCHAR(25) NOT NULL,
	`timestamp` INT NOT NULL,
	`handler` VARCHAR(20) NULL,
	`message` TEXT NOT NULL,
	KEY `queue_name_ix` (`queue`),
	KEY `queue_handler_ix` (`handler`)
) ENGINE=MyISAM;
