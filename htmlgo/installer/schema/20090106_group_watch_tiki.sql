#2008-01-06 lphuberdeau
CREATE TABLE tiki_group_watches (
  `watchId` int(12) NOT NULL auto_increment,
  `group` varchar(200) NOT NULL default '',
  event varchar(40) NOT NULL default '',
  object varchar(200) NOT NULL default '',
  title varchar(250) default NULL,
  type varchar(200) default NULL,
  url varchar(250) default NULL,
  KEY `watchId` (`watchId`),
  PRIMARY KEY (`group`(50),event,object(100))
) ENGINE=MyISAM;
