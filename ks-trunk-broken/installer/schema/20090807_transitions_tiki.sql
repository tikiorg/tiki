-- 2009-08-07 lphuberdeau

INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_trigger_transition', 'Can trigger the transition between two states', 'admin', 'transition', NULL, 'feature_group_transition,feature_category_transition');

CREATE TABLE `tiki_transitions` (
	`transitionId` int NOT NULL AUTO_INCREMENT,
	`name` varchar(50),
	`preserve` int(1) NOT NULL DEFAULT 0,
	`type` varchar(20) NOT NULL,
	`from` varchar(255) NOT NULL,
	`to` varchar(255) NOT NULL,
	PRIMARY KEY(`transitionId`),
	KEY `transition_lookup` (`type`, `from`)
) ENGINE=MyISAM;

