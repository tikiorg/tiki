# $Header: /cvsroot/tikiwiki/tiki/db/tiki_1.8to1.9.sql,v 1.47 2004-04-28 06:20:31 mose Exp $

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

# added on 2004-02-28 by mose for multi-purpose logging facility going with lib/logs/logslib.php

CREATE TABLE tiki_logs (
  logId int(8) NOT NULL auto_increment,
  logtype varchar(20) NOT NULL,
  logmessage text NOT NULL,
  loguser varchar(200) NOT NULL,
  logip varchar(200) NOT NULL,
  logclient text NOT NULL,
  logtime int(14) NOT NULL,
  PRIMARY KEY  (logId),
  KEY logtype (logtype)
) TYPE=MyISAM;

#
# Table structure for table `tiki_shoutbox_words`
#
# Added by damian aka damosoft

CREATE TABLE `tiki_shoutbox_words` (
  word VARCHAR( 40 ) NOT NULL ,
  qty INT DEFAULT '0' NOT NULL ,
  PRIMARY KEY ( `word` )
) TYPE=MyISAM;
# --------------------------------------------------------
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Shoutbox','tiki-admin_shoutbox_words.php',1191,'','tiki_p_admin_shoutbox','');
# --------------------------------------------------------

# added on 2004-03-02 by mose for more details in groups
ALTER TABLE `users_groups` ADD `usersFieldId` INT( 11 ), ADD `groupFieldId` INT( 11 );

# added on 2004-03-09 by mose for another option of tracker field
ALTER TABLE `tiki_tracker_fields` ADD `isMandatory` varchar ( 1 ) DEFAULT 'n' NOT NULL ;
UPDATE `tiki_tracker_fields` set `isMandatory`='y' where `isMandatory`='';

# added on 2004-03-09 by mose for keeping track of what happens
DROP TABLE IF EXISTS `tiki_logs`;
CREATE TABLE tiki_logs (
  logId int(8) NOT NULL auto_increment,
  logtype varchar(20) NOT NULL,
  logmessage text NOT NULL,
  loguser varchar(200) NOT NULL,
  logip varchar(200) NOT NULL,
  logclient text NOT NULL,
  logtime int(14) NOT NULL,
  PRIMARY KEY  (logId),
  KEY logtype (logtype)
) TYPE=MyISAM;

# added on 2004-03-24 by mose for fixing an error in perm label
UPDATE `users_permissions` set `permDesc`='Can view trackers closed items' where `permName`='tiki_p_view_trackers_closed';

# added on 2004-04-02 by mose : more fields for articles and submissions
ALTER TABLE `tiki_articles` ADD `topline` VARCHAR( 255 ) AFTER `articleId` ;
ALTER TABLE `tiki_articles` ADD `subtitle` VARCHAR( 255 ) AFTER `title` ;
ALTER TABLE `tiki_articles` ADD `linkto` VARCHAR( 255 ) AFTER `subtitle` ;
ALTER TABLE `tiki_articles` ADD `image_caption` TEXT AFTER `image_name` ;
ALTER TABLE `tiki_submissions` ADD `topline` VARCHAR( 255 ) AFTER `subId` ;
ALTER TABLE `tiki_submissions` ADD `subtitle` VARCHAR( 255 ) AFTER `title` ;
ALTER TABLE `tiki_submissions` ADD `linkto` VARCHAR( 255 ) AFTER `subtitle` ;
ALTER TABLE `tiki_submissions` ADD `image_caption` TEXT AFTER `image_name` ;

# added on 2004-04-09 by mose
ALTER TABLE `tiki_articles` ADD `lang` VARCHAR( 16 ) AFTER `linkto` ;
ALTER TABLE `tiki_submissions` ADD `lang` VARCHAR( 16 ) AFTER `linkto` ;

ALTER TABLE `tiki_article_types` ADD `show_topline` CHAR( 1 ) AFTER `show_size` ;
ALTER TABLE `tiki_article_types` ADD `show_subtitle` CHAR( 1 ) AFTER `show_topline` ;
ALTER TABLE `tiki_article_types` ADD `show_linkto` CHAR( 1 ) AFTER `show_subtitle` ;
ALTER TABLE `tiki_article_types` ADD `show_image_caption` CHAR( 1 ) AFTER `show_linkto` ;
ALTER TABLE `tiki_article_types` ADD `show_lang` CHAR( 1 ) AFTER `show_image_caption` ;

# added on 2004-04-10 by lphuberdeau - Permissions for tiki sheet
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_admin_sheet', 'Can admin sheet', 'admin', 'sheet');
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_edit_sheet', 'Can create and edit sheets', 'editors', 'sheet');
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_view_sheet', 'Can view sheet', 'basic', 'sheet');
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_view_sheet_history', 'Can view sheet history', 'admin', 'sheet');

INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_sheet','n');

# added on 2004-04-11 by mose at dgd request
ALTER TABLE `tiki_tracker_fields` CHANGE `name` `name` VARCHAR( 255 ) DEFAULT NULL ;

# added on 2004-04-12 by lphuberdeau - TikiSheet base tables
CREATE TABLE tiki_sheet_values (
  sheetId int(8) NOT NULL default '0',
  begin int(10) NOT NULL default '0',
  end int(10) default NULL,
  rowIndex int(4) NOT NULL default '0',
  columnIndex int(4) NOT NULL default '0',
  value varchar(255) default NULL,
  calculation varchar(255) default NULL,
  width int(4) NOT NULL default '1',
  height int(4) NOT NULL default '1',
  UNIQUE KEY sheetId (sheetId,begin,rowIndex,columnIndex),
  KEY sheetId_2 (sheetId,rowIndex,columnIndex)
) TYPE=MyISAM;

CREATE TABLE tiki_sheets (
  sheetId int(8) NOT NULL auto_increment,
  title varchar(200) NOT NULL default '',
  description text,
  author varchar(200) NOT NULL default '',
  PRIMARY KEY  (sheetId)
) TYPE=MyISAM;

#added on 2004-4-13 sylvie
ALTER TABLE `tiki_language` CHANGE `lang` `lang` char(16) NOT NULL default '';
ALTER TABLE `tiki_languages` CHANGE `lang` `lang` char(16) NOT NULL default '';
ALTER TABLE `tiki_calendar_items` CHANGE `lang` `lang` char(16) NOT NULL default 'en';
ALTER TABLE `tiki_menu_languages` CHANGE `language` `language` char(16) NOT NULL default '';
ALTER TABLE `tiki_untranslated` CHANGE `lang` `lang` char(16) NOT NULL default '';

#added on 2004-04-16 franck
ALTER TABLE `tiki_quicktags` ADD `tagcategory` CHAR( 255 ) AFTER `tagicon` ;
ALTER TABLE `tiki_quicktags` ADD INDEX `tagcategory` (`tagcategory`);
UPDATE `tiki_quicktags` set `tagcategory`='wiki';

#added on 2004-4-26 sylvie
DELETE FROM `tiki_preferences` WHERE `name`='email_encoding';
ALTER TABLE `tiki_pages` ADD `lang` VARCHAR( 16 ) AFTER `page_size` ;

#added on 2004-4-27 ggeller
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Homework','tiki-hw_teacher_assignments.php','270','feature_homework','tiki_p_hw_teacher','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Assignments','tiki-hw_teacher_assignments.php','272','feature_homework','tiki_p_hw_teacher','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Grading Queue','tiki-hw_teacher_grading_queue.php','274','feature_homework','tiki_p_hw_teacher','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Last Changes','tiki-hw_teacher_last_changes.php','276','feature_homework','tiki_p_hw_teacher','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Homework','tiki-hw_student_assignments.php','280','feature_homework','tiki_p_hw_student','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Assignments','tiki-hw_teacher_assignments.php','282','feature_homework','tiki_p_hw_student','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Last Changes','tiki-hw_teacher_assignments.php','284','feature_homework','tiki_p_hw_student','');

#added on 2004-4-27 ggeller
INSERT INTO users_permissions(permName, permDesc, level, type) VALUES ('tiki_p_hw_admin','Can adminsiter homework','admin','homework');
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_hw_teacher','Can create new homework assignments, see student names and grade assignments','editors','homework');
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_hw_grader','Can grade homework assignments','editors','homework');
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_hw_student','Can do homework assignments','registered','homework');

#
# Homework tables start
#
#added on 2004-4-27 ggeller
#

CREATE TABLE hw_actionlog (
  action varchar(255) NOT NULL default '',
  lastModif int(14) NOT NULL default '0',
  pageId int(14) default NULL,
  user varchar(200) default NULL,
  ip varchar(15) default NULL,
  comment varchar(200) default NULL,
  PRIMARY KEY  (lastModif)
) TYPE=MyISAM;

CREATE TABLE hw_assignments (
  assignmentId int(8) NOT NULL auto_increment,
  title varchar(80) default NULL,
  teacherName varchar(40) NOT NULL default '',
  created int(14) NOT NULL default '0',
  dueDate int(14) default NULL,
  modified int(14) NOT NULL default '0',
  heading text,
  body text,
  deleted tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (assignmentId),
  KEY dueDate (dueDate)
) TYPE=MyISAM;

CREATE TABLE hw_grading_queue (
  id int(14) NOT NULL auto_increment,
  status int(4) default NULL,
  submissionDate int(14) default NULL,
  userLogin varchar(40) NOT NULL default '',
  userIp varchar(15) default NULL,
  pageId int(14) default NULL,
  pageDate int(14) default NULL,
  pageVersion int(14) default NULL,
  assignmentId int(14) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

CREATE TABLE hw_grading_queue (
  id int(14) NOT NULL auto_increment,
  status int(4) default NULL,
  submissionDate int(14) default NULL,
  userLogin varchar(40) NOT NULL default '',
  userIp varchar(15) default NULL,
  pageId int(14) default NULL,
  pageDate int(14) default NULL,
  pageVersion int(14) default NULL,
  assignmentId int(14) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

CREATE TABLE hw_history (
  id int(14) NOT NULL default '0',
  version int(8) NOT NULL default '0',
  lastModif int(14) NOT NULL default '0',
  user varchar(200) NOT NULL default '',
  ip varchar(15) NOT NULL default '',
  comment varchar(200) default NULL,
  data text,
  PRIMARY KEY  (id,version)
) TYPE=MyISAM;

CREATE TABLE hw_pages (
  id int(14) NOT NULL auto_increment,
  assignmentId int(14) NOT NULL default '0',
  studentName varchar(200) NOT NULL default '',
  data text,
  description varchar(200) default NULL,
  lastModif int(14) default NULL,
  user varchar(200) default NULL,
  comment varchar(200) default NULL,
  version int(8) NOT NULL default '0',
  ip varchar(15) default NULL,
  flag char(1) default NULL,
  points int(8) default NULL,
  votes int(8) default NULL,
  cache text,
  wiki_cache int(10) default '0',
  cache_timestamp int(14) default NULL,
  page_size int(10) unsigned default '0',
  lockUser varchar(200) default NULL,
  lockExpires int(14) default '0',
  PRIMARY KEY  (studentName,assignmentId),
  KEY id (id),
  KEY assignmentId (assignmentId),
  KEY studentName (studentName)
) TYPE=MyISAM;

#
# Homework tables end
#
