# $Header: /cvsroot/tikiwiki/tiki/db/tiki_1.8to1.9.sql,v 1.28 2004-02-24 09:12:55 lfagundes Exp $

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
  name varchar(80) NOT NULL default '',
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

# added on 2004-02-04 by mose for more power in trackers
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_view_trackers_pending', 'Can view trackers pending items', 'editors', 'trackers');

# added on 2004-02-04 by mose (options for tracker fields)
ALTER TABLE `tiki_tracker_fields` CHANGE `isPublic` `isPublic` CHAR( 1 ) DEFAULT 'n' NOT NULL ;
ALTER TABLE `tiki_tracker_fields` ADD `isHidden` varchar ( 1 ) DEFAULT 'n' NOT NULL ;
UPDATE `tiki_tracker_fields` set `isHidden`='y' where `isHidden`='';

# added on 2004-02-10 by mose for more power to dennis daniels
ALTER TABLE `tiki_trackers` CHANGE `name` `name` VARCHAR( 255 ) DEFAULT NULL ;

# added on 2004-02-11 by mose for yet-another-perm
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_view_trackers_closed', 'Can view trackers pending items', 'registered', 'trackers');



#
# Score and karma tables start
#
# Created on Feb 24 2004
#

CREATE TABLE tiki_score (
  event varchar(40) NOT NULL default '',
  score int(11) NOT NULL default '0',
  expiration int(11) NOT NULL default '0',
  category text NOT NULL,
  description text NOT NULL,
  ord int(11) NOT NULL default '0',
  PRIMARY KEY  (event),
  KEY ord (ord)
) TYPE=MyISAM;

CREATE TABLE users_score (
  user char(40) NOT NULL default '',
  event_id char(40) NOT NULL default '',
  score int(11) NOT NULL default '0',
  expire datetime NOT NULL default '0000-00-00 00:00:00',
  tstamp timestamp(14) NOT NULL,
  PRIMARY KEY  (user,event_id),
  KEY user (user,event_id,expire)
) TYPE=MyISAM;


INSERT INTO tiki_score VALUES ('login',1,0,'General','Login',1);
INSERT INTO tiki_score VALUES ('login_remain',2,60,'General','Stay logged',2);
INSERT INTO tiki_score VALUES ('profile_fill',10,0,'General','Fill each profile field',3);
INSERT INTO tiki_score VALUES ('profile_see',2,0,'General','See other user\'s profile',4);
INSERT INTO tiki_score VALUES ('profile_is_seen',1,0,'General','Have your profile seen',5);
INSERT INTO tiki_score VALUES ('friend_new',10,0,'General','Make friends (feature not available yet)',6);
INSERT INTO tiki_score VALUES ('message_receive',1,0,'General','Receive message',7);
INSERT INTO tiki_score VALUES ('message_send',2,0,'General','Send message',8);
INSERT INTO tiki_score VALUES ('article_read',2,0,'Articles','Read an article',9);
INSERT INTO tiki_score VALUES ('article_comment',5,0,'Articles','Comment an article',10);
INSERT INTO tiki_score VALUES ('article_new',20,0,'Articles','Publish an article',11);
INSERT INTO tiki_score VALUES ('article_is_read',1,0,'Articles','Have your article read',12);
INSERT INTO tiki_score VALUES ('article_is_commented',2,0,'Articles','Have your article commented',13);
INSERT INTO tiki_score VALUES ('fgallery_new',10,0,'File galleries','Create new file gallery',14);
INSERT INTO tiki_score VALUES ('fgallery_new_file',10,0,'File galleries','Upload new file to gallery',15);
INSERT INTO tiki_score VALUES ('fgallery_download',5,0,'File galleries','Download other user\'s file',16);
INSERT INTO tiki_score VALUES ('fgallery_is_downloaded',5,0,'File galleries','Have your file downloaded',17);
INSERT INTO tiki_score VALUES ('igallery_new',10,0,'Image galleries','Create a new image gallery',18);
INSERT INTO tiki_score VALUES ('igallery_new_img',6,0,'Image galleries','Upload new image to gallery',19);
INSERT INTO tiki_score VALUES ('igallery_see_img',3,0,'Image galleries','See other user\'s image',20);
INSERT INTO tiki_score VALUES ('igallery_img_seen',1,0,'Image galleries','Have your image seen',21);
INSERT INTO tiki_score VALUES ('blog_new',20,0,'Blogs','Create new blog',22);
INSERT INTO tiki_score VALUES ('blog_post',5,0,'Blogs','Post in a blog',23);
INSERT INTO tiki_score VALUES ('blog_read',2,0,'Blogs','Read other user\'s blog',24);
INSERT INTO tiki_score VALUES ('blog_comment',2,0,'Blogs','Comment other user\'s blog',25);
INSERT INTO tiki_score VALUES ('blog_is_read',3,0,'Blogs','Have your blog read',26);
INSERT INTO tiki_score VALUES ('blog_is_commented',3,0,'Blogs','Have your blog commented',27);
INSERT INTO tiki_score VALUES ('wiki_new',10,0,'Wiki','Create a new wiki page',28);
INSERT INTO tiki_score VALUES ('wiki_edit',5,0,'Wiki','Edit an existing page',29);
INSERT INTO tiki_score VALUES ('wiki_attach_file',3,0,'Wiki','Attach file',30);


#
# Score and karma tables end
#
