# $Header: /cvsroot/tikiwiki/tiki/db/tiki_1.8to1.9.sql,v 1.22 2004-01-31 11:57:06 damosoft Exp $

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

# Tiki Jukebox

INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Jukebox','tiki-jukebox_albums.php',620,'feature_jukebox','tiki_p_jukebox_albums', '');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','View Tracks','tiki-jukebox_tracks.php',625,'feature_jukebox','tiki_p_jukebox_tracks', '');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Upload Tracks','tiki-jukebox_upload.php',630,'feature_jukebox','tiki_p_jukebox_upload', '');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Admin','tiki-jukebox_admin.php',635,'feature_jukebox','tiki_p_jukebox_admin', '');

# Jukebox permissions - Damosoft aka Damian
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_jukebox_albums', 'Can view jukebox albums', 'registered', 'jukebox');
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_jukebox_tracks', 'Can view jukebox tracklist', 'registered', 'jukebox');
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_jukebox_upload', 'Can upload new jukebox tracks', 'registered', 'jukebox');
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_jukebox_admin', 'Can admin the jukebox system', 'admin', 'jukebox');
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_jukebox_genres', 'Can admin the jukebox genres', 'admin', 'jukebox');

# Tiki Jukebox tables (damian)
CREATE TABLE tiki_jukebox_genres (
        genreId int(14) unsigned NOT NULL auto_increment,
        genreName varchar(80),
        genreDescription text,
        PRIMARY KEY (genreId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

CREATE TABLE tiki_jukebox_albums (
        albumId int(14) unsigned NOT NULL auto_increment,
        title varchar(80) default NULL,
        description text,
        created int(14),
        lastModif int(14),
        user varchar(200),
        visits int(14),
        public char(1),
        genreId int(14),
        PRIMARY KEY(albumId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

CREATE TABLE tiki_jukebox_tracks (
        trackId int(14) unsigned NOT NULL auto_increment,
        albumId int(14),
        artist varchar(200),
        title varchar(200),
        created int(14),
        url varchar(255),
        filename varchar(80),
        filesize int(14),
        filetype varchar(250),
        genreId int(14),
        plays int(14),
        PRIMARY KEY(trackId)
) TYPE=MyISAM AUTO_INCREMENT=1;


# added on 2004-01-22 by mose, changing trackers options
CREATE TABLE tiki_tracker_options (
  trackerId int(12) NOT NULL default '0',
  name varchar(80) default NULL,
  value text default NULL,
  PRIMARY KEY (trackerId,name(30))
) TYPE=MyISAM ;

# added on 2004-01-23 by mose, adding a field param
ALTER TABLE tiki_tracker_fields ADD isPublic varchar ( 1 ) default NULL;

# added on 2004-01-28 by mose, make it behave like before
ALTER TABLE `tiki_tracker_fields` CHANGE `isPublic` `isPublic` CHAR( 1 ) DEFAULT 'y' NOT NULL ;
UPDATE `tiki_tracker_fields` set `isPublic`='y' where `isPublic`='';


# Added on 28 Jan 2004 by rlpowell, to allow for changing your vote in a poll.
ALTER TABLE `tiki_user_votings` ADD optionId int(10) NOT NULL default '0';

# Added on 29 Jan 2004 by Swillie, to hold choice for displaying user avatar on post heading
ALTER TABLE `tiki_blogs` ADD `show_avatar` char(1) default NULL;
