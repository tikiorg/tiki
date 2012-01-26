CREATE TABLE `tiki_object_relations` (
	`relationId` INT PRIMARY KEY AUTO_INCREMENT,
	`relation` varchar(25) NOT NULL,
	`source_type` varchar(50) NOT NULL,
	`source_itemId` varchar(255) NOT NULL,
	`target_type` varchar(50) NOT NULL,
	`target_itemId` varchar(255) NOT NULL,
	KEY `relation_source_ix` (`source_type`, `source_itemId`),
	KEY `relation_target_ix` (`target_type`, `target_itemId`)
);
