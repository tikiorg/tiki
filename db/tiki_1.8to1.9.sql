# $Header: /cvsroot/tikiwiki/tiki/db/tiki_1.8to1.9.sql,v 1.10 2004-01-17 21:04:08 gongo Exp $

# The following script will update a tiki database from verion 1.7 to 1.8
# 
# To execute this file do the following:
#
# $ mysql -f dbname < tiki_1.8to1.9.sql
#
# where dbname is the name of your tiki database.
#
# For example, if your tiki database is named tiki (not a bad choice), type:
#
# $ mysql -f tiki < tiki_1.8to1.9.sql
# 
# You may execute this command as often as you like, 
# and may safely ignore any error messages that appear.


ALTER TABLE tiki_mailin_accounts ADD anonymous char(1) NOT NULL default 'y';

INSERT INTO tiki_preferences(name,value) VALUES ('feature_mantis','n');
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_mantis_admin', 'Can admin Mantis configuration', 'admin', 'mantis');
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_mantis_view', 'Can view Mantis bugs', 'registered', 'mantis');
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_admin_packager', 'Can admin packages/packager', 'admin', 'packages');
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_edit_package', 'Can create packages with packager', 'admin', 'packages');
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_install_package', 'Can install packages', 'admin', 'packages');

INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Mantis','tiki-mantis-main.php',190,'feature_mantis','tiki_p_mantis_view','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','View Bugs','tiki-mantis-view_bugs.php',192,'feature_mantis','tiki_p_mantis_view','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Admin','tiki-mantis-admin.php',198,'feature_mantis','tiki_p_mantis_admin','');

ALTER TABLE tiki_tracker_fields ADD position int(4) default NULL;

ALTER TABLE tiki_tracker_item_attachments CHANGE itemId itemId int(12) NOT NULL default 0;
ALTER TABLE tiki_tracker_item_attachments ADD longdesc blob;
ALTER TABLE tiki_tracker_item_attachments ADD version varchar(40) default NULL;
ALTER TABLE tiki_trackers ADD showComments char(1) default NULL;
ALTER TABLE tiki_trackers ADD orderAttachments varchar(255) NOT NULL default 'filename,created,filesize,downloads,desc';

# added on 2004-01-01 by mose (user and groups dedicated trackers pref)
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('groupTracker','n');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('userTracker','n');

ALTER TABLE `users_groups` ADD `usersTrackerId` INT(11) ;
ALTER TABLE `users_groups` ADD `groupTrackerId` INT(11) ;

# added on 2004-01-03 by mose 
ALTER TABLE `tiki_tracker_fields` ADD `isSearchable` CHAR(1) NOT NULL default 'y';

# added on 2004-01-12 by mose (fix cache for wiki pages)
ALTER  TABLE  `tiki_pages`  modify  `wiki_cache` INT( 10  ) default null;

# added on 2004-01-16 by mose (expanded groupname field)
ALTER TABLE `users_groups` CHANGE `groupName` `groupName` VARCHAR( 255 ) NOT NULL;
ALTER TABLE `users_groups` DROP PRIMARY KEY , ADD PRIMARY KEY ( `groupName` ( 30 ) ); 

ALTER TABLE `users_usergroups` CHANGE `groupName` `groupName` VARCHAR( 255 ) NOT NULL;
ALTER TABLE `users_usergroups` DROP PRIMARY KEY , ADD PRIMARY KEY ( `userId` , `groupName` ( 30 ) );

ALTER TABLE `users_grouppermissions` CHANGE `groupName` `groupName` VARCHAR( 255 ) NOT NULL;
ALTER TABLE `users_grouppermissions` DROP PRIMARY KEY , ADD PRIMARY KEY ( `groupName` ( 30 ), permName );

ALTER TABLE `users_objectpermissions` CHANGE `groupName` `groupName` VARCHAR( 255 ) NOT NULL;
ALTER TABLE `users_objectpermissions` DROP PRIMARY KEY , ADD PRIMARY KEY ( objectId,groupName(30),permName );

ALTER TABLE `tiki_group_inclusion` DROP PRIMARY KEY ;
ALTER TABLE `tiki_group_inclusion` CHANGE `groupName` `groupName` VARCHAR( 255 ) NOT NULL;
ALTER TABLE `tiki_group_inclusion` CHANGE `includeGroup` `includeGroup` VARCHAR( 255 ) NOT NULL;
ALTER TABLE `tiki_group_inclusion` ADD PRIMARY KEY ( groupName(30),includeGroup(30) );

# added on 2004-01-16 by damosoft: new marking a blog entry as private
ALTER TABLE `tiki_blog_posts` ADD `priv` VARCHAR( 1 );





