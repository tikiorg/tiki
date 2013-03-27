CREATE TABLE IF NOT EXISTS `tiki_mail_queue` (
  `messageId` INT NOT NULL AUTO_INCREMENT ,
  `message`   TEXT NULL ,
  `attempts`  INT NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`messageId`) 
) ENGINE=MyISAM AUTO_INCREMENT=1;