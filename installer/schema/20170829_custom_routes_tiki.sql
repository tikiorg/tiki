CREATE TABLE `tiki_custom_route` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `description` varchar(255) NULL,
  `type` varchar(255) NOT NULL,
  `from` varchar(255) NOT NULL,
  `redirect` text NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `user_id` int(11) NOT NULL
) ENGINE=MyISAM;
