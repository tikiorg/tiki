#2010-01-14 sept_7
#change ip to store ipv6 addresses
ALTER TABLE `tiki_actionlog` CHANGE `ip` `ip` VARCHAR( 39 );
#add client to store USER_AGENT
ALTER TABLE `tiki_actionlog` ADD `client` VARCHAR( 200 ) NULL DEFAULT NULL;
#change actionlog config
UPDATE `tiki_actionlog_conf` SET `action`='login', `objectType`='system' where `action`='*' and `objectType`='login';
UPDATE `tiki_actionlog_conf` SET `action`='%' where `action`='*';
#integrate tiki_logs in tiki_actionlog
INSERT INTO `tiki_actionlog` (`action`,`lastModif`,`object`,`objectType`,`user`,`ip`,`comment`,`categId`,`client`) SELECT `logtype`,`logtime`,'system','system',`loguser`,`logip`,`logmessage`,0,SUBSTRING(`logclient`,1,200) FROM tiki_logs;
