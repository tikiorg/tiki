#2008-11-20 JohannesMoser
ALTER TABLE `tiki_polls` ADD `anonym` ENUM( 'a', 'u', 'i', 'c' ) NOT NULL DEFAULT 'u';
CREATE TABLE IF NOT EXISTS `tiki_poll_votes` (
  `pollId` int(11) NOT NULL,
  `optionId` int(11) NOT NULL,
  `voteId` int(11) NOT NULL auto_increment,
  `identification` varchar(300) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY  (`voteId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;