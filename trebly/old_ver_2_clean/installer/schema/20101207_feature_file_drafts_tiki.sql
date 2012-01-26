CREATE TABLE IF NOT EXISTS `tiki_file_drafts` (
  `fileId` int(14) NOT NULL,
  `filename` varchar(80) default NULL,
  `filesize` int(14) default NULL,
  `filetype` varchar(250) default NULL,
  `data` longblob,
  `user` varchar(200) default '',
  `path` varchar(255) default NULL,
  `hash` varchar(32) default NULL,
  `lastModif` integer(14) DEFAULT NULL,
  `lockedby` varchar(200) default '',
  PRIMARY KEY (`fileId`, `user`)
) ENGINE=MyISAM;