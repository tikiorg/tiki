-- 2009-10-21 lphuberdeau - Upstreaming from Mozilla

CREATE TABLE IF NOT EXISTS `tiki_page_lists` (
  `list_type_id` int(8) unsigned NOT NULL,
  `priority` int(8) unsigned NOT NULL,
  `page_name` varchar(160) NOT NULL,
  `score` float default NULL,
  PRIMARY KEY  (`list_type_id`,`page_name`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `tiki_page_list_types` (
  `id` int(8) unsigned NOT NULL auto_increment,
  `name` varchar(40) NOT NULL,
  `title` varchar(160) default NULL,
  `description` varchar(200) default NULL,
  PRIMARY KEY  (`name`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

