
CREATE TABLE `tiki_activity_stream_rules` (
  `ruleId` int(8) NOT NULL auto_increment,
  `eventType` varchar(100) NOT NULL,
  `ruleType` varchar(20) NOT NULL,
  `rule` TEXT,
  `notes` TEXT,
  PRIMARY KEY(`ruleId`)
) ENGINE=MyISAM;

