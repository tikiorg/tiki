CREATE TABLE `tiki_user_monitors` (
	`monitorId` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`userId` INT NOT NULL,
	`event` VARCHAR(50) NOT NULL,
	`priority` VARCHAR(10) NOT NULL,
	`target` VARCHAR(15) NOT NULL,
	INDEX `userid_target_ix` (`userId`, `target`),
	UNIQUE `event_target_uq` (`event`, `target`, `userId`)
) ENGINE=MyISAM;
