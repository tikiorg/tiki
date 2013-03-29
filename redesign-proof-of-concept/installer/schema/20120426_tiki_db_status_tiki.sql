CREATE TABLE IF NOT EXISTS `tiki_db_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `objectId` varchar(100) NOT NULL,
  `tableName` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL,
  `other` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;