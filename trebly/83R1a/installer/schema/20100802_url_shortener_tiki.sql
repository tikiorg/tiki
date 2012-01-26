DROP TABLE IF EXISTS `tiki_url_shortener`;
CREATE TABLE `tiki_url_shortener` (
  `urlId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(200) NOT NULL,
  `longurl` tinytext NOT NULL,
  `longurl_hash` varchar(32) NOT NULL,
  `service` varchar(32) NOT NULL,
  `shorturl` varchar(63) NOT NULL,
  PRIMARY KEY (`urlId`),
  UNIQUE KEY `shorturl` (`shorturl`),
  KEY `longurl_hash` (`longurl_hash`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;