
CREATE TABLE IF NOT EXISTS `tiki_activity_stream` (
  `activityId` int(8) NOT NULL auto_increment,
  `eventType` varchar(100) NOT NULL,
  `eventDate` int NOT NULL,
  `arguments` BLOB,
  PRIMARY KEY(`activityId`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `tiki_activity_stream_mapping` (
  `field_name` varchar(50) NOT NULL,
  `field_type` varchar(15) NOT NULL,
  PRIMARY KEY(`field_name`)
) ENGINE=MyISAM;

