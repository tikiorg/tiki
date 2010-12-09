CREATE TABLE `tiki_object_attributes` (
	`attributeId` INT PRIMARY KEY AUTO_INCREMENT,
	`type` varchar(50) NOT NULL,
	`itemId` varchar(255) NOT NULL,
	`attribute` varchar(25) NOT NULL,
	`value` varchar(100),
	UNIQUE `item_attribute_uq` ( `type`, `itemId`, `attribute` ),
	KEY `attribute_lookup_ix` (`attribute`, `value`)
);
