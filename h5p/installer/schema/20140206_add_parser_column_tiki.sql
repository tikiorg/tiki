CREATE TABLE IF NOT EXISTS `tiki_output` (
  `entityId` varchar(160) NOT NULL default '',
  `objectType` varchar(32) NOT NULL default '',
  `outputType` varchar(32) NOT NULL default '',
  `version` int(8) NOT NULL default '0',
  `outputId` INT NOT NULL PRIMARY KEY AUTO_INCREMENT
) ENGINE=MyISAM AUTO_INCREMENT=1;
