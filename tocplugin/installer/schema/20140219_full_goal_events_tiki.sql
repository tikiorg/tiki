
-- Undo previous patch - same day
DROP TABLE IF EXISTS `tiki_goal_events`;
CREATE TABLE `tiki_goal_events` (
	`eventId` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`eventDate` INT NOT NULL,
	`eventType` VARCHAR(50) NOT NULL,
	`targetType` VARCHAR(50),
	`targetObject` VARCHAR(255),
	`user` VARCHAR(200) NOT NULL,
	`groups` BLOB NOT NULL
) ENGINE=MyISAM;
