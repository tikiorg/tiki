#2008-12-11 niclone
CREATE TABLE `tiki_sent_newsletters_files` (
  `id` int(11) NOT NULL auto_increment,
  `editionId` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `type` varchar(64) NOT NULL,
  `size` int(11) NOT NULL,
  `filename` varchar(256) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `editionId` (`editionId`)
);
