CREATE TABLE IF NOT EXISTS `tiki_user_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(200) COLLATE latin1_general_ci NOT NULL,
  `interval` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `view` varchar(8) COLLATE latin1_general_ci NOT NULL,
  `type` varchar(5) COLLATE latin1_general_ci NOT NULL,
  `time_to_send` datetime NOT NULL,
  `always_email` tinyint(1) NOT NULL,
  `last_report` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `tiki_user_reports_cache` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(200) COLLATE latin1_general_ci NOT NULL,
  `event` varchar(200) COLLATE latin1_general_ci NOT NULL,
  `data` text COLLATE latin1_general_ci NOT NULL,
  `time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;