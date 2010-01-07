#gillesm 2008-10-27
#2008_10_26 GillesM
CREATE TABLE tiki_groupalert (	`groupName` varchar(255) NOT NULL default '', `objectType` varchar( 20 ) NOT NULL default '', `objectId`  varchar(10) NOT NULL default '', `displayEachuser`  char( 1 ) default NULL , PRIMARY KEY ( `groupName`,`objectType`,`objectId`) ) ENGINE=MyISAM ;
