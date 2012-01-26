CREATE TABLE `tiki_todo` (
	`todoId` INT(12) NOT NULL auto_increment,
	`after` INT(12) NOT NULL,
	`event` ENUM('creation', 'modification', 'upload'),
	`objectType` VARCHAR(50),
	`objectId` VARCHAR(255) default NULL,
	`from` VARCHAR(255) default NULL,
	`to` VARCHAR(255) default NULL,
	PRIMARY KEY (`todoId`),
	KEY `what` (`objectType`, `objectId`),
	KEY `after` (`after`)
);
CREATE TABLE `tiki_todo_notif` (
	`todoId` INT(12) NOT NULL,
	`objectId` VARCHAR(255) default NULL,
	KEY `todoId` (`todoId`),
	KEY `objectId` (`objectId`)
);
