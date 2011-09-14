CREATE TABLE `tiki_tracker_item_field_logs` (
  `version` int(12) NOT NULL,
  `itemId` int(12) NOT NULL default '0',
  `fieldId` int(12) NOT NULL default '0',
  `value` text,
  `lang` char(16) default NULL,
  INDEX `version` (`version`),
  INDEX `itemId` (`itemId`),
  INDEX `fieldId` (`itemId`)
) ENGINE=MyISAM;
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Updated', 'trackeritem', 'n');
INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('Created', 'trackeritem', 'n');