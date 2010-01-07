# $Id$

# The following script will update a tiki database from version 1.8.x to 1.9.x
# The following script will ALSO update from version 1.9.x to 1.9.y
# 
# Ex.: If you upgrading from Tiki 1.9.2 to 1.9.4, you NEED to run this script. 
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

# added on 2005-02-01 by kyori (new features for Galaxia Workflow)
ALTER TABLE galaxia_instances ADD name varchar(200) default 'No Name' NOT NULL AFTER started;
ALTER TABLE galaxia_activities ADD expirationTime int(6) unsigned NOT NULL default 0;

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

# added on 2004-12-06 by burley (new ldap URL preference, optional).
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('auth_ldap_url','');


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

#added on 2006-05-12 by wesleywillians
INSERT INTO users_permissions (permName,permDesc,level,type) values ('tiki_p_admin_rssmodules','Can admin rss modules', 'admin', 'tiki');
insert into users_permissions (permName,permDesc,level,type) values ('tiki_p_admin_polls','Can admin polls', 'admin', 'tiki');
insert into users_permissions (permName,permDesc,level,type) values ('tiki_p_admin_objects','Can edit object permissions', 'admin', 'tiki');

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


INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('login',1,0,'General','Login',1);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('login_remain',2,60,'General','Stay logged',2);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('profile_fill',10,0,'General','Fill each profile field',3);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('profile_see',2,0,'General','See other user\'s profile',4);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('profile_is_seen',1,0,'General','Have your profile seen',5);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('friend_new',10,0,'General','Make friends (feature not available yet)',6);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('message_receive',1,0,'General','Receive message',7);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('message_send',2,0,'General','Send message',8);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('article_read',2,0,'Articles','Read an article',9);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('article_comment',5,0,'Articles','Comment an article',10);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('article_new',20,0,'Articles','Publish an article',11);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('article_is_read',1,0,'Articles','Have your article read',12);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('article_is_commented',2,0,'Articles','Have your article commented',13);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('fgallery_new',10,0,'File galleries','Create new file gallery',14);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('fgallery_new_file',10,0,'File galleries','Upload new file to gallery',15);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('fgallery_download',5,0,'File galleries','Download other user\'s file',16);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('fgallery_is_downloaded',5,0,'File galleries','Have your file downloaded',17);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('igallery_new',10,0,'Image galleries','Create a new image gallery',18);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('igallery_new_img',6,0,'Image galleries','Upload new image to gallery',19);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('igallery_see_img',3,0,'Image galleries','See other user\'s image',20);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('igallery_img_seen',1,0,'Image galleries','Have your image seen',21);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('blog_new',20,0,'Blogs','Create new blog',22);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('blog_post',5,0,'Blogs','Post in a blog',23);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('blog_read',2,0,'Blogs','Read other user\'s blog',24);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('blog_comment',2,0,'Blogs','Comment other user\'s blog',25);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('blog_is_read',3,0,'Blogs','Have your blog read',26);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('blog_is_commented',3,0,'Blogs','Have your blog commented',27);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('wiki_new',10,0,'Wiki','Create a new wiki page',28);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('wiki_edit',5,0,'Wiki','Edit an existing page',29);
INSERT INTO tiki_score (event,score,expiration,category,description,ord) VALUES ('wiki_attach_file',3,0,'Wiki','Attach file',30);


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
DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='o' and name='Shoutbox' and url='tiki-admin_shoutbox_words.php' and position='1191' and section='' and perm='tiki_p_admin_shoutbox' and groupname='' ;
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Shoutbox','tiki-admin_shoutbox_words.php',1191,'','tiki_p_admin_shoutbox','');
# --------------------------------------------------------

# added on 2004-03-02 by mose for more details in groups
ALTER TABLE `users_groups` ADD `usersFieldId` INT( 11 ), ADD `groupFieldId` INT( 11 );

# added on 2004-03-09 by mose for another option of tracker field
ALTER TABLE `tiki_tracker_fields` ADD `isMandatory` varchar ( 1 ) DEFAULT 'n' NOT NULL ;
UPDATE `tiki_tracker_fields` set `isMandatory`='y' where `isMandatory`='';

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

ALTER TABLE `tiki_article_types` ADD `show_topline` CHAR( 1 ) DEFAULT 'n' AFTER `show_size` ;
ALTER TABLE `tiki_article_types` ADD `show_subtitle` CHAR( 1 ) DEFAULT 'n' AFTER `show_topline` ;
ALTER TABLE `tiki_article_types` ADD `show_linkto` CHAR( 1 ) DEFAULT 'n' AFTER `show_subtitle` ;
ALTER TABLE `tiki_article_types` ADD `show_image_caption` CHAR( 1 ) DEFAULT 'n' AFTER `show_linkto` ;
ALTER TABLE `tiki_article_types` ADD `show_lang` CHAR( 1 ) DEFAULT 'n' AFTER `show_image_caption` ;

# added on 2004-04-10 by lphuberdeau - Permissions for tiki sheet
INSERT INTO `users_permissions` (permName, permDesc, level, type) VALUES ('tiki_p_admin_sheet', 'Can admin sheet', 'admin', 'sheet');
INSERT INTO `users_permissions` (permName, permDesc, level, type) VALUES ('tiki_p_edit_sheet', 'Can create and edit sheets', 'editors', 'sheet');
INSERT INTO `users_permissions` (permName, permDesc, level, type) VALUES ('tiki_p_view_sheet', 'Can view sheet', 'basic', 'sheet');
INSERT INTO `users_permissions` (permName, permDesc, level, type) VALUES ('tiki_p_view_sheet_history', 'Can view sheet history', 'admin', 'sheet');

INSERT IGNORE INTO `tiki_preferences` (name,value) VALUES ('feature_sheet','n');

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
ALTER TABLE `tiki_quicktags` ADD INDEX `taglabel` (`taglabel`);

UPDATE `tiki_quicktags` set `tagcategory`='wiki' where `tagcategory` is NULL;

DELETE FROM `tiki_quicktags` WHERE `taglabel`='New wms Metadata' AND `taginsert`='METADATA\r\n		\"wms_name\" \"myname\"\r\n		\"wms_srs\" \"EPSG:4326\"\r\n	\"wms_server_version\" \" \"\r\n	\"wms_layers\" \"mylayers\"\r\n	\"wms_request\" \"myrequest\"\r\n	\"wms_format\" \" \"\r\n	\"wms_time\" \" \"\r\n END' AND `tagicon`='img/icons/admin_metatags.png' AND `tagcategory`='maps';
INSERT INTO `tiki_quicktags` (taglabel, taginsert, tagicon, tagcategory) VALUES ('New wms Metadata','METADATA\r\n		\"wms_name\" \"myname\"\r\n		\"wms_srs\" \"EPSG:4326\"\r\n	\"wms_server_version\" \" \"\r\n	\"wms_layers\" \"mylayers\"\r\n	\"wms_request\" \"myrequest\"\r\n	\"wms_format\" \" \"\r\n	\"wms_time\" \" \"\r\n END','img/icons/admin_metatags.png', 'maps');
DELETE FROM `tiki_quicktags` WHERE `taglabel`='New Class' AND `taginsert`='CLASS\r\n EXPRESSION ()\r\n SYMBOL 0\r\n OUTLINECOLOR\r\n COLOR\r\n  NAME \"myclass\"\r\nEND #end of class' AND `tagicon`='img/icons/mini_triangle.gif' AND `tagcategory`='maps';
INSERT INTO `tiki_quicktags` (taglabel, taginsert, tagicon, tagcategory) VALUES ('New Class','CLASS\r\n EXPRESSION ()\r\n SYMBOL 0\r\n OUTLINECOLOR\r\n COLOR\r\n  NAME \"myclass\"\r\nEND #end of class','img/icons/mini_triangle.gif', 'maps');
DELETE FROM `tiki_quicktags` WHERE `taglabel`='New Projection' AND `taginsert`='PROJECTION\r\n \"init=epsg:4326\"\r\nEND' AND `tagicon`='images/ico_mode.gif' AND `tagcategory`='maps';
INSERT INTO `tiki_quicktags` (taglabel, taginsert, tagicon, tagcategory) VALUES ('New Projection','PROJECTION\r\n \"init=epsg:4326\"\r\nEND','images/ico_mode.gif', 'maps');
DELETE FROM `tiki_quicktags` WHERE `taglabel`='New Query' AND `taginsert`='#\r\n#Start of query definitions\r\n QUERYMAP\r\n STATUS ON\r\n STYLE HILITE\r\nEND' AND `tagicon`='img/icons/question.gif' AND `tagcategory`='maps';
INSERT INTO `tiki_quicktags` (taglabel, taginsert, tagicon, tagcategory) VALUES ('New Query','#\r\n#Start of query definitions\r\n QUERYMAP\r\n STATUS ON\r\n STYLE HILITE\r\nEND','img/icons/question.gif', 'maps');
DELETE FROM `tiki_quicktags` WHERE `taglabel`='New Scalebar' AND `taginsert`='#\r\n#start of scalebar\r\nSCALEBAR\r\n IMAGECOLOR 255 255 255\r\n STYLE 1\r\n SIZE 400 2\r\n COLOR 0 0 0\r\n  UNITS KILOMETERS\r\n INTERVALS 5\r\n STATUS ON\r\nEND' AND `tagicon`='img/icons/desc_lenght.gif' AND `tagcategory`='maps';
INSERT INTO `tiki_quicktags` (taglabel, taginsert, tagicon, tagcategory) VALUES ('New Scalebar','#\r\n#start of scalebar\r\nSCALEBAR\r\n IMAGECOLOR 255 255 255\r\n STYLE 1\r\n SIZE 400 2\r\n COLOR 0 0 0\r\n  UNITS KILOMETERS\r\n INTERVALS 5\r\n STATUS ON\r\nEND','img/icons/desc_lenght.gif', 'maps');
DELETE FROM `tiki_quicktags` WHERE `taglabel`='New Layer' AND `taginsert`='LAYER\r\n NAME \"mylayer\"\r\n TYPE\r\n STATUS ON\r\n DATA \"mydata\"\r\nEND #end of layer' AND `tagicon`='img/ed_copy.gif' AND `tagcategory`='maps';
INSERT INTO `tiki_quicktags` (taglabel, taginsert, tagicon, tagcategory) VALUES ('New Layer', 'LAYER\r\n NAME \"mylayer\"\r\n TYPE\r\n STATUS ON\r\n DATA \"mydata\"\r\nEND #end of layer', 'img/ed_copy.gif', 'maps');
DELETE FROM `tiki_quicktags` WHERE `taglabel`='New Label' AND `taginsert`='LABEL\r\n  COLOR\r\n ANGLE\r\n FONT arial\r\n TYPE TRUETYPE\r\n  POSITION\r\n  PARTIALS TRUE\r\n  SIZE 6\r\n  BUFFER 0\r\n OUTLINECOLOR\r\nEND #end of label' AND `tagicon`='img/icons/fontfamily.gif' AND `tagcategory`='maps';
INSERT INTO `tiki_quicktags` (taglabel, taginsert, tagicon, tagcategory) VALUES ('New Label','LABEL\r\n  COLOR\r\n ANGLE\r\n FONT arial\r\n TYPE TRUETYPE\r\n  POSITION\r\n  PARTIALS TRUE\r\n  SIZE 6\r\n  BUFFER 0\r\n OUTLINECOLOR\r\nEND #end of label','img/icons/fontfamily.gif', 'maps');
DELETE FROM `tiki_quicktags` WHERE `taglabel`='New Reference' AND `taginsert`='#\r\n#start of reference\r\nREFERENCE\r\n SIZE 120 60\r\n STATUS ON\r\n  EXTENT -180 -90 182 88\r\n OUTLINECOLOR 255 0 0\r\n IMAGE \"myimagedata\"\r\nCOLOR -1 -1 -1\r\nEND' AND `tagicon`='images/ed_image.gif' AND `tagcategory`='maps';
INSERT INTO `tiki_quicktags` (taglabel, taginsert, tagicon, tagcategory) VALUES ('New Reference','#\r\n#start of reference\r\nREFERENCE\r\n SIZE 120 60\r\n STATUS ON\r\n  EXTENT -180 -90 182 88\r\n OUTLINECOLOR 255 0 0\r\n IMAGE \"myimagedata\"\r\nCOLOR -1 -1 -1\r\nEND','images/ed_image.gif', 'maps');
DELETE FROM `tiki_quicktags` WHERE `taglabel`='New Legend' AND `taginsert`='#\r\n#start of legend\r\n#\r\nLEGENDr\n KEYSIZE 18 12\r\n POSTLABELCACHE TRUE\r\n STATUS ON\r\nEND' AND `tagicon`='images/ed_about.gif' AND `tagcategory`='maps';
INSERT INTO `tiki_quicktags` (taglabel, taginsert, tagicon, tagcategory) VALUES ('New Legend','#\r\n#start of legend\r\n#\r\nLEGENDr\n KEYSIZE 18 12\r\n POSTLABELCACHE TRUE\r\n STATUS ON\r\nEND','images/ed_about.gif', 'maps');
DELETE FROM `tiki_quicktags` WHERE `taglabel`='New Web' AND `taginsert`='#\r\n#Start of web interface definition\r\n#\r\nWEB\r\n TEMPLATE \"myfile/url\"\r\n MINSCALE 1000\r\n MAXSCALE 40000\r\n IMAGEPATH \"myimagepath\"\r\n IMAGEURL \"mypath\"\r\nEND' AND `tagicon`='img/icons/ico_link.gif' AND `tagcategory`='maps';
INSERT INTO `tiki_quicktags` (taglabel, taginsert, tagicon, tagcategory) VALUES ('New Web','#\r\n#Start of web interface definition\r\n#\r\nWEB\r\n TEMPLATE \"myfile/url\"\r\n MINSCALE 1000\r\n MAXSCALE 40000\r\n IMAGEPATH \"myimagepath\"\r\n IMAGEURL \"mypath\"\r\nEND','img/icons/ico_link.gif', 'maps');
DELETE FROM `tiki_quicktags` WHERE `taglabel`='New Outputformat' AND `taginsert`='OUTPUTFORMAT\r\n NAME\r\n DRIVER \" \"\r\n MIMETYPE \"myimagetype\"\r\n IMAGEMODE RGB\r\n EXTENSION \"png\"\r\nEND' AND `tagicon`='img/icons/opera.gif' AND `tagcategory`='maps';
INSERT INTO `tiki_quicktags` (taglabel, taginsert, tagicon, tagcategory) VALUES ('New Outputformat','OUTPUTFORMAT\r\n NAME\r\n DRIVER \" \"\r\n MIMETYPE \"myimagetype\"\r\n IMAGEMODE RGB\r\n EXTENSION \"png\"\r\nEND','img/icons/opera.gif', 'maps');
DELETE FROM `tiki_quicktags` WHERE `taglabel`='New Mapfile' AND `taginsert`='#\r\n#Start of mapfile\r\n#\r\nNAME MYMAPFILE\r\n STATUS ON\r\nSIZE \r\nEXTENT\r\n UNITS\r\nSHAPEPATH \" \"\r\nIMAGETYPE \" \"\r\nFONTSET \" \"\r\nIMAGECOLOR -1 -1 -1\r\n\r\n#remove this text and add objects here\r\n\r\nEND # end of mapfile' AND `tagicon`='img/icons/global.gif' AND `tagcategory`='maps';
INSERT INTO `tiki_quicktags` (taglabel, taginsert, tagicon, tagcategory) VALUES ('New Mapfile','#\r\n#Start of mapfile\r\n#\r\nNAME MYMAPFILE\r\n STATUS ON\r\nSIZE \r\nEXTENT\r\n UNITS\r\nSHAPEPATH \" \"\r\nIMAGETYPE \" \"\r\nFONTSET \" \"\r\nIMAGECOLOR -1 -1 -1\r\n\r\n#remove this text and add objects here\r\n\r\nEND # end of mapfile', 'img/icons/global.gif', 'maps');

#added on 2004-04-19 lphuberdeau - Additional table for TikiSheet
CREATE TABLE tiki_sheet_layout (
  sheetId int(8) NOT NULL default '0',
  begin int(10) NOT NULL default '0',
  end int(10) default NULL,
  headerRow int(4) NOT NULL default '0',
  footerRow int(4) NOT NULL default '0',
  className varchar(64) default NULL,
  UNIQUE KEY sheetId (sheetId,begin)
) TYPE=MyISAM;

#added on 2004-4-26 sylvie
DELETE FROM `tiki_preferences` WHERE `name`='email_encoding';
ALTER TABLE `tiki_pages` ADD `lang` VARCHAR( 16 ) AFTER `page_size` ;

#added on 2004-4-27 ggeller

DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='s' and name='Homework' and url='tiki-hw_teacher_assignments.php' and position='270' and section='feature_homework' and perm='tiki_p_hw_teacher' and groupname='';
# INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Homework','tiki-hw_teacher_assignments.php','270','feature_homework','tiki_p_hw_teacher','');
DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='o' and name='Assignments' and url='tiki-hw_teacher_assignments.php' and position='272' and section='feature_homework' and perm='tiki_p_hw_teacher' and groupname='';
# INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Assignments','tiki-hw_teacher_assignments.php','272','feature_homework','tiki_p_hw_teacher','');
DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='o' and name='Grading Queue' and url='tiki-hw_teacher_grading_queue.php' and position='274' and section='feature_homework' and perm='tiki_p_hw_teacher' and groupname='';
# INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Grading Queue','tiki-hw_teacher_grading_queue.php','274','feature_homework','tiki_p_hw_teacher','');
DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='o' and name='Last Changes' and url='tiki-hw_teacher_last_changes.php' and position='276' and section='feature_homework' and perm='tiki_p_hw_teacher' and groupname='';
# INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Last Changes','tiki-hw_teacher_last_changes.php','276','feature_homework','tiki_p_hw_teacher','');
DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='s' and name='Homework' and url='tiki-hw_student_assignments.php' and position='280' and section='feature_homework' and perm='tiki_p_hw_student' and groupname='';
# INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Homework','tiki-hw_student_assignments.php','280','feature_homework','tiki_p_hw_student','');
DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='o' and name='Assignments' and url='tiki-hw_teacher_assignments.php' and position='282' and section='feature_homework' and perm='tiki_p_hw_student' and groupname='';
# INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Assignments','tiki-hw_teacher_assignments.php','282','feature_homework','tiki_p_hw_student','');
DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='o' and name='Last Changes' and url='tiki-hw_teacher_assignments.php' and position='284' and section='feature_homework' and perm='tiki_p_hw_student' and groupname='';
# INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Last Changes','tiki-hw_teacher_assignments.php','284','feature_homework','tiki_p_hw_student','');

#added on 2004-4-27 ggeller
# INSERT INTO users_permissions(permName, permDesc, level, type) VALUES ('tiki_p_hw_admin','Can adminsiter homework','admin','homework');
# INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_hw_teacher','Can create new homework assignments, see student names and grade assignments','editors','homework');
# INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_hw_grader','Can grade homework assignments','editors','homework');
# INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_hw_student','Can do homework assignments','registered','homework');

#
# Homework tables start
#
#added on 2004-4-27 ggeller
#

#  CREATE TABLE tiki_hw_actionlog (
#    action varchar(255) NOT NULL default '',
#    lastModif int(14) NOT NULL default '0',
#    pageId int(14) default NULL,
#    user varchar(40) default NULL,
#    ip varchar(15) default NULL,
#    comment varchar(200) default NULL,
#    PRIMARY KEY  (lastModif)
#  ) TYPE=MyISAM;

#  CREATE TABLE tiki_hw_assignments (
#    assignmentId int(8) NOT NULL auto_increment,
#    title varchar(80) default NULL,
#    teacherName varchar(40) NOT NULL default '',
#    created int(14) NOT NULL default '0',
#    dueDate int(14) default NULL,
#    modified int(14) NOT NULL default '0',
#    heading text,
#    body text,
#    deleted tinyint(4) NOT NULL default '0',
#    PRIMARY KEY  (assignmentId),
#    KEY dueDate (dueDate)
#  ) TYPE=MyISAM;

#  CREATE TABLE tiki_hw_grading_queue (
#    id int(14) NOT NULL auto_increment,
#    status int(4) default NULL,
#    submissionDate int(14) default NULL,
#    userLogin varchar(40) NOT NULL default '',
#    userIp varchar(15) default NULL,
#    pageId int(14) default NULL,
#    pageDate int(14) default NULL,
#    pageVersion int(14) default NULL,
#    assignmentId int(14) default NULL,
#    PRIMARY KEY  (id)
#  ) TYPE=MyISAM;

#  CREATE TABLE tiki_hw_history (
#    id int(14) NOT NULL default '0',
#    version int(8) NOT NULL default '0',
#    lastModif int(14) NOT NULL default '0',
#    user varchar(40) NOT NULL default '',
#    ip varchar(15) NOT NULL default '',
#    comment varchar(200) default NULL,
#    data text,
#    PRIMARY KEY  (id,version)
#  ) TYPE=MyISAM;

#  CREATE TABLE tiki_hw_pages_history (
#    id int(14) NOT NULL default '0',
#    version int(8) NOT NULL default '0',
#    lastModif int(14) NOT NULL default '0',
#    user varchar(40) NOT NULL default '',
#    ip varchar(15) NOT NULL default '',
#    comment varchar(200) default NULL,
#    data text,
#    PRIMARY KEY  (id,version)
#  ) TYPE=MyISAM;

#  CREATE TABLE tiki_hw_pages (
#    id int(14) NOT NULL auto_increment,
#    assignmentId int(14) NOT NULL default '0',
#    studentName varchar(200) NOT NULL default '',
#    data text,
#    description varchar(200) default NULL,
#    lastModif int(14) default NULL,
#    user varchar(40) default NULL,
#    comment varchar(200) default NULL,
#    version int(8) NOT NULL default '0',
#    ip varchar(15) default NULL,
#    flag char(1) default NULL,
#    points int(8) default NULL,
#    votes int(8) default NULL,
#    cache text,
#    wiki_cache int(10) default '0',
#    cache_timestamp int(14) default NULL,
#    page_size int(10) unsigned default '0',
#    lockUser varchar(200) default NULL,
#    lockExpires int(14) default '0',
#    PRIMARY KEY  (studentName,assignmentId),
#    KEY id (id),
#    KEY assignmentId (assignmentId),
#    KEY studentName (studentName)
#  ) TYPE=MyISAM;

#
# Homework tables end
#

INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_send_newsletters', 'Can send newsletters', 'editors', 'newsletters');


#
# Improved Quizzes start
#

ALTER TABLE `tiki_quizzes` ADD `immediateFeedback` char(1) default NULL ;
ALTER TABLE `tiki_quizzes` ADD `showAnswers` char(1) default NULL ;
ALTER TABLE `tiki_quizzes` ADD `shuffleQuestions` char(1) default NULL ;
ALTER TABLE `tiki_quizzes` ADD `shuffleAnswers` char(1) default NULL ;
ALTER TABLE `tiki_quizzes` ADD `publishDate` int(14) default NULL;
ALTER TABLE `tiki_quizzes` ADD `expireDate` int(14) default NULL;
ALTER TABLE `tiki_quizzes` ADD `bDeleted` char(1) default NULL;
ALTER TABLE `tiki_quizzes` ADD `nVersion` int(4) NOT NULL ;
ALTER TABLE `tiki_quizzes` ADD `nAuthor` int(4) default NULL;
ALTER TABLE `tiki_quizzes` ADD `bOnline` char(1) default NULL;
ALTER TABLE `tiki_quizzes` ADD `bRandomQuestions` char(1) default NULL;
ALTER TABLE `tiki_quizzes` ADD `nRandomQuestions` tinyint(4) default NULL;
ALTER TABLE `tiki_quizzes` ADD `bLimitQuestionsPerPage` char(1) default NULL;
ALTER TABLE `tiki_quizzes` ADD `nLimitQuestionsPerPage` tinyint(4) default NULL;
ALTER TABLE `tiki_quizzes` ADD `bMultiSession` char(1) default NULL;
ALTER TABLE `tiki_quizzes` ADD `nCanRepeat` tinyint(4) default NULL;
ALTER TABLE `tiki_quizzes` ADD `sGradingMethod` varchar(80) default NULL;
ALTER TABLE `tiki_quizzes` ADD `sShowScore` varchar(80) default NULL;
ALTER TABLE `tiki_quizzes` ADD `sShowCorrectAnswers` varchar(80) default NULL;
ALTER TABLE `tiki_quizzes` ADD `sPublishStats` varchar(80) default NULL;
ALTER TABLE `tiki_quizzes` ADD `bAdditionalQuestions` char(1) default NULL;
ALTER TABLE `tiki_quizzes` ADD `bForum` char(1) default NULL;
ALTER TABLE `tiki_quizzes` ADD `sForum` varchar(80) default NULL;
ALTER TABLE `tiki_quizzes` ADD `sPrologue` text default NULL;
ALTER TABLE `tiki_quizzes` ADD `sData` text default NULL;
ALTER TABLE `tiki_quizzes` ADD `sEpilogue` text default NULL;
ALTER TABLE `tiki_quizzes` ADD `passingperct` int(4) default 0;


CREATE TABLE `tiki_user_answers_uploads` (
  `answerUploadId` int(4) NOT NULL auto_increment,
  `userResultId` int(11) NOT NULL default '0',
  `questionId` int(11) NOT NULL default '0',
  `filename` varchar(255) NOT NULL default '',
  `filetype` varchar(64) NOT NULL default '',
  `filesize` varchar(255) NOT NULL default '',
  `filecontent` longblob NOT NULL,
  PRIMARY KEY  (`answerUploadId`)
) TYPE=MyISAM;


#
# Improved Quizzes end
#

# Added 1 May 2004 by Robin Lee Powell; anonymous poll votes allowed or not.
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_poll_anonymous','n');

# Added 2004 on 2004-05-17 by chealer : 876510 fix
UPDATE `tiki_preferences` set `name`='image_galleries_comments_default_order' where `name`='image_galleries_comments_default_orderin';

# Added 29 May 2004 by lfagundes; was in cvs but not in database
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_wiki_open_as_structure','n');

# 
# Changing language code from 'br' to 'pt-br'
# 29 May 2004 by lfagundes
# 

UPDATE tiki_calendar_items SET lang = 'pt-br' WHERE lang='br';
UPDATE tiki_language SET lang = 'pt-br' WHERE lang='br';
UPDATE tiki_languages SET lang = 'pt-br' WHERE lang='br';
UPDATE tiki_menu_languages SET language = 'pt-br' WHERE language='br';
UPDATE tiki_untranslated SET lang = 'pt-br' WHERE lang='br';
UPDATE tiki_preferences SET value = 'pt-br' WHERE value='br' and name='language';
UPDATE tiki_user_preferences SET value = 'pt-br' WHERE value='br' and prefName='language';

# Added 29 May 2004 by lfagundes; new feature
# Modified 30 May 2004 by sylvie; I think the default must be 'n' to have the same functionality than before by default and to be coherent with the tiki-setup
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_detect_language','n');

# Added 30 May 2004 by lfagundes; serialized empty array
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('available_languages','a:0:{}');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('available_styles','a:0:{}');

# Added on June 5th 2004 by lphuberdeau; Field to hold the cell format

ALTER TABLE tiki_sheet_values ADD format varchar(255) default NULL;

#Added 6 June 2004 by sylvie
#translated objects table
CREATE TABLE tiki_translated_objects (
  traId int(14) NOT NULL auto_increment,
  type varchar(50) NOT NULL,
  objId varchar(255) NOT NULL,
  lang varchar(16) default NULL,
  PRIMARY KEY (type, objId),
  KEY traId ( traId )
) TYPE=MyISAM AUTO_INCREMENT=1;


# Added on June 8th 2004 by lfagundes; Friendship Network
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_friends','n');
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_list_users', 'Can list registered users', 'registered', 'community');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('user_list_order','score_desc');

CREATE TABLE tiki_friends (
  user char(40) NOT NULL default '',
  friend char(40) NOT NULL default '',
  PRIMARY KEY  (user,friend)
) TYPE=MyISAM;

CREATE TABLE tiki_friendship_requests (
  userFrom char(40) NOT NULL default '',
  userTo char(40) NOT NULL default '',
  tstamp timestamp(14) NOT NULL,
  PRIMARY KEY  (userFrom,userTo)
) TYPE=MyISAM;

#Added June13th 2004 sylvie
UPDATE tiki_pages set lang=null where lang="NULL";

#Added June13th 2004 lfagundes
ALTER TABLE users_score RENAME TO tiki_users_score;
ALTER TABLE users_users ADD score int4 NOT NULL default 0;
ALTER TABLE users_users ADD KEY (score);
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_score','n');

# Added June 15th sylvie
DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='s' and name='Community' and url='tiki-list_users.php' and position='187' and section='feature_friends' and perm='tiki_p_list_users' and groupname='';
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Community','tiki-list_users.php','187','feature_friends','tiki_p_list_users','');
DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='o' and name='Member list' and url='tiki-list_users.php' and position='188' and section='feature_friends' and perm='tiki_p_list_users' and groupname='';
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Member list','tiki-list_users.php','188','feature_friends','tiki_p_list_users','');
DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='o' and name='Friendship Network' and url='tiki-friends.php' and position='189' and section='feature_friends' and perm='' and groupname='';
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Friendship Network','tiki-friends.php','189','feature_friends','','');

# Added June 15th fhcorrea
ALTER TABLE `tiki_articles` ADD `bibliographical_references` TEXT DEFAULT NULL AFTER `created` , ADD `resume` TEXT DEFAULT NULL AFTER `bibliographical_references`;
ALTER TABLE `tiki_submissions` ADD `bibliographical_references` TEXT DEFAULT NULL AFTER `created` , ADD `resume` TEXT DEFAULT NULL AFTER `bibliographical_references`;

# Added June 17th terence (added "article-put" mail-in account type)
ALTER TABLE `tiki_mailin_accounts` ADD `article_topicId` int(4) DEFAULT NULL , ADD `article_type` varchar(50) DEFAULT NULL;

#Added June 23th lfagundes aka batawata, sorting section
update `tiki_menu_options` set type='r' where `menuId`=42 and `name`='Admin (click!)';

#Added June 26th lfagundes aka batawata, making score db independent
alter table `tiki_users_score` modify `expire` int(14) not null;

#Added June 27th lfagundes, refactoring score to have static data in php instead of db
alter table `tiki_score` drop description;
alter table `tiki_score` drop category;
alter table `tiki_score` drop ord;

#Added June 27th lfagundes, removing uneeded
alter table `tiki_users_score` drop score;

#Added June 27th marclaporte, changing menu option to take into account new tiki_p_send_newsletters perm
# uncomment and use these if you didnt alter the default Application menu 
#UPDATE tiki_menu_options SET perm = 'tiki_p_send_newsletters' WHERE position='905';
#UPDATE users_permissions SET level = 'admin' WHERE permName='tiki_p_admin_newsletters';
#UPDATE users_permissions SET level = 'editors' WHERE permName='tiki_p_send_newsletters';

CREATE TABLE IF NOT EXISTS tiki_searchsyllable(
  syllable varchar(80) NOT NULL default '',
  lastUsed int(11) NOT NULL default '0',
  lastUpdated int(11) NOT NULL default '0',
  PRIMARY KEY  (syllable),
  KEY lastUsed (lastUsed)
) TYPE=MyISAM;

CREATE TABLE  IF NOT EXISTS tiki_searchwords(
  syllable varchar(80) NOT NULL default '',
  searchword varchar(80) NOT NULL default '',
  PRIMARY KEY  (syllable,searchword)
) TYPE=MyISAM;

# added 29 06 04 06:24:56 by mose for more optoins in wiki
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_wiki_userpage','y');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_wiki_userpage_prefix','UserPage');

#added 7/12/04 sylvie
UPDATE `tiki_menu_options` set `name`='Upload file' where `name`='Upload  File' and menuId='42';
UPDATE `tiki_menu_options` set `name`='MyTiki' where `name`='MonTiki (clic!)' and menuId='42';
DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='o' and name='MyTiki home' and url='tiki-my_tiki.php' and position='51' and section='' and perm='' and groupname='Registered';
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','MyTiki home','tiki-my_tiki.php',51,'','','Registered');
UPDATE `tiki_menu_options` set `name`='Admin' where `name`='Admin (click!)' and menuId='42';
DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='o' and name='Admin home' and url='tiki-admin.php' and position='1051' and section='' and perm='tiki_p_admin' and groupname='';
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Admin home','tiki-admin.php',1051,'','tiki_p_admin','');
DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='o' and name='System Admin' and url='tiki-admin_system.php' and position='1230' and section='' and perm='tiki_p_admin' and groupname='';
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','System Admin','tiki-admin_system.php',1230,'','tiki_p_admin','');
UPDATE `tiki_menu_options` set `name`='Shoutbox Words' where `position`='1191' and menuId='42';
DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='o' and name='Score' and url='tiki-admin_score.php' and position='1235' and section='' and perm='tiki_p_admin' and groupname='';
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Score','tiki-admin_score.php',1235,'','tiki_p_admin','');
UPDATE `tiki_menu_options` set `name`='User list' where `url`='tiki-list_users.php' and menuId='42';
UPDATE `tiki_menu_options` set `section`='feature_articles,feature_cms_rankings'  where `section`='feature_articles,feature_cms_ranking' and menuId='42';
DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='s' and name='TikiSheet' and url='tiki-sheets.php' and position='780' and section='feature_sheet' and perm='' and groupname='';
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','TikiSheet','tiki-sheets.php',780,'feature_sheet','','');
UPDATE `tiki_menu_options` set `url`='tiki-blog_rankings.php' where `url`='tiki-blogs_rankings.php' and menuId='42';

INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('contact_anon','n');

#revised  20040903 ggeller
# INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_homework','n');

#added 20040906 chris_holman
CREATE TABLE tiki_structure_versions (
  structure_id int(14) NOT NULL auto_increment,
  version int(14) default NULL,
  PRIMARY KEY  (structure_id)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

ALTER TABLE `tiki_structures` ADD `structure_id` int(14) NOT NULL AFTER `page_ref_id`;
ALTER TABLE `tiki_structures` ADD `page_version` int(8) default NULL AFTER `page_id`;

# added on 2004-09-08 by chealer for installs originally made between 1.8.0 and 1.8.2 : provides tiki_download table
CREATE TABLE `tiki_download` (
  `id` int(11) NOT NULL auto_increment,
  `object` varchar(255) NOT NULL default '',
  `userId` int(8) NOT NULL default '0',
  `type` varchar(20) NOT NULL default '',
  `date` int(14) NOT NULL default '0',
  `IP` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `object` (`object`,`userId`,`type`),
  KEY `userId` (`userId`),
  KEY `type` (`type`),
  KEY `date` (`date`)
) TYPE=MyISAM ;

# added on 2004-09-18 by franck for adding geographic capability to image galleries
ALTER TABLE `tiki_galleries` ADD `geographic` char(1) default NULL AFTER `visible`;
ALTER TABLE `tiki_images` ADD `lat` float default NULL AFTER `description`;
ALTER TABLE `tiki_images` ADD `lon` float default NULL AFTER `description`;

# added on 07 10 04 00:01:12 by mose
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_wiki_view_comments', 'Can view wiki comments', 'basic', 'wiki');

# added on 11 10 04 06:28:29 by more for registration validation by admin
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('validateRegistration','n');

# added on 2004-10-19 by redflo. ported subgalleries and other new features to 1.9
ALTER TABLE tiki_galleries ADD COLUMN (
        sortorder VARCHAR(20) NOT NULL DEFAULT 'created',
        sortdirection VARCHAR(4) NOT NULL DEFAULT 'desc',
        galleryimage VARCHAR(20) NOT NULL DEFAULT 'first',
        parentgallery int(14) NOT NULL default -1,
        showname char(1) NOT NULL DEFAULT 'y',
        showimageid char(1) NOT NULL DEFAULT 'n',
        showdescription char(1) NOT NULL DEFAULT 'n',
        showcreated char(1) NOT NULL DEFAULT 'n',
        showuser char(1) NOT NULL DEFAULT 'n',
        showhits char(1) NOT NULL DEFAULT 'y',
        showxysize char(1) NOT NULL DEFAULT 'y',
        showfilesize char(1) NOT NULL DEFAULT 'n',
        showfilename char(1) NOT NULL DEFAULT 'n',
        defaultscale varchar(10) NOT NULL DEFAULT 'o'
);

alter table tiki_galleries_scales add column (scale int(11) NOT NULL default 0);
update tiki_galleries_scales set scale=greatest(xsize,ysize);
alter table tiki_galleries_scales drop primary key;
alter table tiki_galleries_scales drop column xsize;
alter table tiki_galleries_scales drop column ysize;
alter table tiki_galleries_scales add primary key (galleryId,scale);

# added on 2004-10-20 by gg. added event registration to 1.9
# removed on 2005-04-01 by gg. removed poor implementation of events

alter table `tiki_calendars` drop `customevent`;
alter table `tiki_calendar_items` drop `evId`;
drop table if exists tiki_event_subscription;
drop table if exists tiki_events;
drop table if exists tiki_sent_events;

# added on 2005-03-26 by jmj
ALTER TABLE `tiki_calendar_items` ADD `nlId` INT( 12 ) DEFAULT '0' NOT NULL AFTER `categoryId` ;
ALTER TABLE `tiki_calendars` ADD `customsubscription` ENUM('n','y') DEFAULT 'n' NOT NULL AFTER `customparticipants` ;


#added on 2004-10-22 by sylvie
ALTER TABLE `tiki_pages` ADD `lockedby` VARCHAR(200) default NULL;
ALTER TABLE  `tiki_pages` DROP column `lock`;

#added on 2004-10-23 by sylvie
UPDATE `tiki_user_watches` set `event`='article_submitted', `object`='*' where `event`='article_post';
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('default_wiki_diff_style','minsidediff');

#added on 2004-10-24 by redflo
ALTER TABLE `tiki_images_data` ADD `etag` varchar(32) default NULL;

INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('https','auto');
	  
ALTER TABLE `tiki_quicktags` CHANGE `taginsert` `taginsert` TEXT DEFAULT NULL;

alter table tiki_user_modules add parse char(1) default NULL;
UPDATE tiki_user_modules SET parse='n' WHERE name='menu_application_menu';

# Now change the mod-application_menu to the database based version
# added damian aka damosoft
INSERT IGNORE INTO `tiki_user_modules`(name,title,data, parse)  VALUES ('mnu_application_menu', 'Menu', '{menu id=42}', 'n');
UPDATE `tiki_modules` set `name`='mnu_application_menu' where `name`='mod-application_menu' or `name`='application_menu';

# added on 2005-02-24 by mdavey
UPDATE `tiki_modules` set `params`='flip=y' where `name`='mnu_application_menu' and (`params` IS NULL or `params`='');

# added damian aka damosoft
UPDATE `tiki_user_assigned_modules` set `name`='mnu_application_menu' where `name`='mod-application_menu' or `name`='application_menu';

# added on 12 10 04 08:21:46 by mose for wiki_rating system
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_wiki_view_ratings', 'Can view rating of wiki pages', 'basic', 'wiki');
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_wiki_vote_ratings', 'Can participate to rating of wiki pages', 'registered', 'wiki');
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_wiki_admin_ratings', 'Can add and change ratings on wiki pages', 'admin', 'wiki');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_wiki_ratings','n');

CREATE TABLE `tiki_poll_objects` (
  `catObjectId` int(11) NOT NULL default '0',
  `pollId` int(11) NOT NULL default '0',
  `title` varchar(255) default NULL,
  PRIMARY KEY  (`catObjectId`,`pollId`)
) TYPE=MyISAM;

ALTER TABLE `tiki_poll_options` ADD `position` INT( 4 ) DEFAULT '0' NOT NULL AFTER `title` ;

DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='o' and name='Admin mods' and url='tiki-mods.php' and position='1240' and section='' and perm='tiki_p_admin' and groupname='';
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Admin mods','tiki-mods.php',1240,'','tiki_p_admin','');
DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='o' and name='Tiki Logs' and url='tiki-syslog.php' and position='1245' and section='' and perm='tiki_p_admin' and groupname='';
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Tiki Logs','tiki-syslog.php',1245,'','tiki_p_admin','');


INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_tracker_view_ratings', 'Can view rating result for tracker items', 'basic', 'trackers');
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_tracker_vote_ratings', 'Can vote a rating for tracker items', 'registered', 'trackers');
CREATE INDEX urlindex ON tiki_link_cache (url(250));

# added on Nov 02 2004 6:06 PM by as6o for file galleries
alter table tiki_files add column search_data longtext;
alter table tiki_files add column lastModif integer(14) DEFAULT NULL;
alter table tiki_files add column lastModifUser varchar(200) DEFAULT NULL;
alter table tiki_files drop KEY ft;
alter table tiki_files add FULLTEXT ft (name, description, search_data);

# added on Nov 02 2004 6:06 PM by as6o for file galleries
CREATE TABLE tiki_file_handlers (
	mime_type varchar(64) default NULL,
	cmd varchar(238) default NULL
) TYPE=MyISAM;

#Dec 08 2004 sylvieg
UPDATE `tiki_menu_options` set `url`='tiki-survey_stats.php' where `url`='tiki-surveys_stats.php';
UPDATE `tiki_menu_options` set `perm`='tiki_p_subscribe_newsletters,tiki_p_send_newsletters,tiki_p_admin_newsletters' where `url`='tiki-newsletters.php';

# 2004-12-10 sylvieg (backport from head)
ALTER TABLE tiki_mailin_accounts add column (discard_after varchar(255) default NULL);
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES('tiki_p_admin_users', 'Can admin users', 'admin', 'user');
UPDATE tiki_menu_options set perm='tiki_p_admin_users' where menuId=42 && name='Users' && perm='tiki_p_admin';

# better do it in the upgrade as well.
INSERT IGNORE INTO tiki_preferences (name,value) VALUES ('mail_crlf','LF');

# 2004-12-16 sylvieg (to synchronise update 1.7 to 1.8 and new db)
ALTER TABLE tiki_blog_posts drop KEY ft;
ALTER TABLE tiki_blog_posts ADD FULLTEXT KEY ft(data, title);
ALTER TABLE tiki_blog_posts MODIFY data_size int(11) unsigned NOT NULL default '0';

# 2004-12-18 sylvieg
ALTER TABLE tiki_calendars ADD personal ENUM ('n', 'y') NOT NULL DEFAULT 'n' AFTER lastmodif;

# 2004-12-31 sylvieg
UPDATE tiki_menu_options set `perm`='tiki_p_live_support_admin' where `perm`='tiki_p_admin_live_support';

#2005-01-02 sylvieg
UPDATE tiki_menu_options set `perm`= 'tiki_p_subscribe_events' where `url`='tiki-events.php';
UPDATE tiki_menu_options set `perm`= 'tiki_p_subscribe_newsletters' where `url`='tiki-newsletters.php';
DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='s' and name='Newsletters' and url='tiki-newsletters.php' and position='900' and section='feature_newsletters' and perm='tiki_p_send_newsletters' and groupname='';
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Newsletters','tiki-newsletters.php',900,'feature_newsletters','tiki_p_send_newsletters','');
DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='s' and name='Newsletters' and url='tiki-newsletters.php' and position='900' and section='feature_newsletters' and perm='tiki_p_admin_newsletters' and groupname='';
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Newsletters','tiki-newsletters.php',900,'feature_newsletters','tiki_p_admin_newsletters','');

#2005-01-07 sylvieg
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('calendar_timezone','n');

#2005-01-09 sylvieg
UPDATE tiki_menu_options set `perm`= 'tiki_p_read_article,tiki_p_admin_received_articles', url='tiki-received_articles.php' where `name`='Received articles';

#2005-01-13 sir-b
#2005-01-25 Hausi
#2005-01-30 sir-b
CREATE TABLE tiki_user_tasks_history (
  belongs_to integer(14) NOT NULL,                   -- the fist task in a history it has the same id as the task id
  task_version integer(4) NOT NULL DEFAULT 0,        -- version number for the history it starts with 0
  title varchar(250) NOT NULL,                       -- title
  description text DEFAULT NULL,                     -- description
  start integer(14) DEFAULT NULL,                    -- date of the starting, if it is not set than there is not starting date
  end integer(14) DEFAULT NULL,                      -- date of the end, if it is not set than there is not dealine
  lasteditor varchar(200) NOT NULL,                  -- lasteditor: username of last editior
  lastchanges integer(14) NOT NULL,                  -- date of last changes
  priority integer(2) NOT NULL DEFAULT 3,            -- priority
  completed integer(14) DEFAULT NULL,                -- date of the completation if it is null it is not yet completed
  deleted integer(14) DEFAULT NULL,                  -- date of the deleteation it it is null it is not deleted
  status char(1) DEFAULT NULL,                       -- null := waiting, 
                                                     -- o := open / in progress, 
                                                     -- c := completed -> (percentage = 100) 
  percentage int(4) DEFAULT NULL,
  accepted_creator char(1) DEFAULT NULL,             -- y - yes, n - no, null - waiting
  accepted_user char(1) DEFAULT NULL,                -- y - yes, n - no, null - waiting
  PRIMARY KEY (belongs_to, task_version)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

UPDATE tiki_user_tasks set title = '-'  where title IS NULL;
INSERT INTO tiki_user_tasks_history (belongs_to, title, start, description, lasteditor, lastchanges, priority, completed, status, percentage) SELECT  taskId, title, date, description, user, date, priority, completed, status, percentage FROM tiki_user_tasks;

ALTER TABLE tiki_user_tasks DROP description;
ALTER TABLE tiki_user_tasks DROP title;
ALTER TABLE tiki_user_tasks ADD last_version integer(4) NOT NULL DEFAULT 0 AFTER taskId;
ALTER TABLE tiki_user_tasks MODIFY user varchar(200) NOT NULL DEFAULT '';
ALTER TABLE tiki_user_tasks ADD creator varchar(200) NOT NULL AFTER user;
ALTER TABLE tiki_user_tasks ADD public_for_group varchar(30) DEFAULT NULL AFTER creator;
ALTER TABLE tiki_user_tasks ADD rights_by_creator char(1) DEFAULT NULL AFTER public_for_group;
ALTER TABLE tiki_user_tasks CHANGE `date` `created` integer(14) NOT NULL;
ALTER TABLE tiki_user_tasks ADD status char(1) default NULL;
ALTER TABLE tiki_user_tasks ADD priority int(2) default NULL;
ALTER TABLE tiki_user_tasks ADD completed int(14) default NULL;
ALTER TABLE tiki_user_tasks ADD percentage int(4) default NULL;
ALTER TABLE tiki_user_tasks ADD UNIQUE KEY (creator,created);

#2005-01-13 sir-b
INSERT INTO users_permissions (permName, permDesc, level,type) VALUES ('tiki_p_tasks_send', 'Can send tasks to other users', 'registered', 'user');
INSERT INTO users_permissions (permName, permDesc, level,type) VALUES ('tiki_p_tasks_receive', 'Can receive tasks from other users', 'registered', 'user');
INSERT INTO users_permissions (permName, permDesc, level,type) VALUES ('tiki_p_tasks_admin', 'Can admin public tasks', 'admin', 'user');

#2005-01-20 sylvieg
CREATE TABLE tiki_newsletter_groups (
  nlId int(12) NOT NULL default '0',
  groupName varchar(255) NOT NULL default '',
  code varchar(20),
  PRIMARY KEY  (nlId,groupName)
) TYPE=MyISAM;
ALTER TABLE tiki_newsletter_subscriptions ADD isUser char(1) NOT NULL default 'n' AFTER subscribed;
UPDATE tiki_newsletter_subscriptions set isUser='n' where isUser='' or isUser IS NULL;
ALTER TABLE tiki_newsletter_subscriptions DROP PRIMARY KEY;
ALTER TABLE tiki_newsletter_subscriptions ADD PRIMARY KEY  (nlId,email,isUser);

#2005-02-07 sylvieg
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('change_password','y');

#2005-02-11 sylvieg
DELETE FROM tiki_quicktags WHERE taglabel='bold' AND taginsert='__text__' AND tagicon='images/ed_format_bold.gif' AND tagcategory='newsletters';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('bold', '__text__', 'images/ed_format_bold.gif', 'newsletters');
DELETE FROM tiki_quicktags WHERE taglabel='italic' AND taginsert='\'\'text\'\'' AND tagicon='images/ed_format_italic.gif' AND tagcategory='newsletters';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('italic', '\'\'text\'\'', 'images/ed_format_italic.gif', 'newsletters');
DELETE FROM tiki_quicktags WHERE taglabel='underline' AND taginsert='===text===' AND tagicon='images/ed_format_underline.gif' AND tagcategory='newsletters';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('underline', '===text===', 'images/ed_format_underline.gif', 'newsletters');
DELETE FROM tiki_quicktags WHERE taglabel='external link' AND taginsert='[http://example.com|text]' AND tagicon='images/ed_link.gif' AND tagcategory='newsletters';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('external link', '[http://example.com|text]', 'images/ed_link.gif', 'newsletters');
DELETE FROM tiki_quicktags WHERE taglabel='heading1' AND taginsert='!text' AND tagicon='images/ed_custom.gif' AND tagcategory='newsletters';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('heading1', '!text', 'images/ed_custom.gif', 'newsletters');
DELETE FROM tiki_quicktags WHERE taglabel='hr' AND taginsert='---' AND tagicon='images/ed_hr.gif' AND tagcategory='newsletters';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('hr', '---', 'images/ed_hr.gif', 'newsletters');
DELETE FROM tiki_quicktags WHERE taglabel='center text' AND taginsert='::text::' AND tagicon='images/ed_align_center.gif' AND tagcategory='newsletters';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('center text', '::text::', 'images/ed_align_center.gif', 'newsletters');
DELETE FROM tiki_quicktags WHERE taglabel='colored text' AND taginsert='~~#FF0000:text~~' AND tagicon='images/fontfamily.gif' AND tagcategory='newsletters';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('colored text', '~~#FF0000:text~~', 'images/fontfamily.gif', 'newsletters');
DELETE FROM tiki_quicktags WHERE taglabel='image' AND taginsert='{img src= width= height= align= desc= link= }' AND tagicon='images/ed_image.gif' AND tagcategory='newsletters';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('image', '{img src= width= height= align= desc= link= }', 'images/ed_image.gif', 'newsletters');

DELETE FROM tiki_quicktags WHERE taglabel='external link' AND taginsert='[http://example.com|text|nocache]' AND tagicon='images/ed_link.gif' AND tagcategory='newsletters';
UPDATE tiki_quicktags set taginsert='[http://example.com|text|nocache]' where taginsert='[http://example.com|text]' and tagcategory='newsletters';

DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='o' and name='Security Admin' and url='tiki-admin_security.php' and position='1250' and section='' and perm='tiki_p_admin' and groupname='';
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Security Admin','tiki-admin_security.php',1250,'','tiki_p_admin','');

#2005-02-20 adding Directory Batch Load feature
DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='o' and name='Directory batch' and url='tiki-batch_upload.php' and position='318' and section='feature_galleries,feature_gal_batch' and perm='tiki_p_batch_upload_image_dir' and groupName='';
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Directory batch','tiki-batch_upload.php',318,'feature_galleries,feature_gal_batch','tiki_p_batch_upload_image_dir','');
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_batch_upload_image_dir', 'Can use Directory Batch Load', 'editors', 'image galleries');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_gal_batch','n');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('gal_batch_dir','');

#2005-04-15 adding Images Cache
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_gal_imgcache','n');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('gal_imgcache_dir','temp/cache');

#2005-02-23 sylvieg
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('calendar_sticky_popup','n');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('calendar_view_tab','n');
DELETE FROM `tiki_preferences` WHERE `name`='calendar_timezone';

#2005-02-26 ohertel
# Table structure for table messu_archive (same structure as messu_messages)
CREATE TABLE messu_archive (
  msgId int(14) NOT NULL auto_increment,
  user varchar(200) NOT NULL default '',
  user_from varchar(200) NOT NULL default '',
  user_to text,
  user_cc text,
  user_bcc text,
  subject varchar(255) default NULL,
  body text,
  hash varchar(32) default NULL,
  date int(14) default NULL,
  isRead char(1) default NULL,
  isReplied char(1) default NULL,
  isFlagged char(1) default NULL,
  priority int(2) default NULL,
  PRIMARY KEY  (msgId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;
# --------------------------------------------------------

# default sizes for mailbox, read box and mail archive
# in messages per user and box (0=unlimited)
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('messu_mailbox_size','0');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('messu_archive_size','200');
#2005-02-27 ohertel
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('messu_sent_size','200');

# Table structure for table messu_sent (same structure as messu_messages)
CREATE TABLE messu_sent (
  msgId int(14) NOT NULL auto_increment,
  user varchar(200) NOT NULL default '',
  user_from varchar(200) NOT NULL default '',
  user_to text,
  user_cc text,
  user_bcc text,
  subject varchar(255) default NULL,
  body text,
  hash varchar(32) default NULL,
  date int(14) default NULL,
  isRead char(1) default NULL,
  isReplied char(1) default NULL,
  isFlagged char(1) default NULL,
  priority int(2) default NULL,
  PRIMARY KEY  (msgId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;
# --------------------------------------------------------

ALTER TABLE messu_messages ADD replyto_hash varchar(32) default NULL AFTER hash;
ALTER TABLE messu_archive ADD replyto_hash varchar(32) default NULL AFTER hash;
ALTER TABLE messu_sent ADD replyto_hash varchar(32) default NULL AFTER hash;

# Moving topic perm into cms where it can be found more easily!
UPDATE users_permissions SET type="cms" WHERE permName='tiki_p_topic_read';

#2005-03-02 sylvieg
UPDATE tiki_menu_options SET name="Admin charts" WHERE url='tiki-admin_charts.php' and name='Charts';

# Added 9 Mar 2005 by Robin Lee Powell; watches also activate on translated versions.
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_user_watches_translations','y');

# 2005-03-16 sylvieg
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('limitedGoGroupHome', 'y');

#2005-03-22 noia
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_map_view_mapfiles', 'Can view contents of mapfiles', 'registered', 'maps');

# 2005-03-26 marclaporte (this should have been optional since the beginning)
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_wiki_import_html', 'n');

# 2005-03-30 mdavey
ALTER TABLE tiki_history ADD version_minor int(8) NOT NULL default 0 AFTER version;

# 2005-04-03 ohertel
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('directory_cool_sites','y');

# 2005-04-07 mose
UPDATE tiki_menu_options SET url="tiki-list_games.php" where name="Games";

# Per-forum outbound mail options.  Added 9 April 2005 by rlpowell
ALTER TABLE `tiki_forums` ADD `outbound_mails_for_inbound_mails` CHAR( 1 ) AFTER `outbound_address` ;
ALTER TABLE `tiki_forums` ADD `outbound_mails_reply_link` CHAR( 1 ) AFTER `outbound_mails_for_inbound_mails`;

# 2005-04-18 melmut
ALTER TABLE `tiki_pages` ADD `is_html` TINYINT(1) default 0; 
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('wiki_wikisyntax_in_html','full');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_wysiwyg','no');

#2005-2-22 moved back to be still be able to assign perm
UPDATE users_permissions SET type="topics" WHERE permName='tiki_p_topic_read';

# 2005_04-23 removed projects 
delete from tiki_menu_options where section='feature_projects';

# 2005-04-24 rss tracker
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('max_rss_tracker','10');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('rss_tracker','n');

# added on 2005-04-24 by ohertel: view wiki history
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_wiki_view_history', 'Can view wiki history', 'basic', 'wiki');

# 2005-04-25 redflo: tiki_secdb for admin->security checks
# DROP TABLE IF EXISTS tiki_secdb;
CREATE TABLE tiki_secdb(
  md5_value varchar(32) NOT NULL,
  filename varchar(250) NOT NULL,
  tiki_version varchar(60) NOT NULL,
  severity int(4) NOT NULL default '0',
  PRIMARY KEY  (md5_value,filename,tiki_version),
  KEY sdb_fn (filename)
) TYPE=MyISAM;

update tiki_menu_options set section="feature_featuredLinks" where url="tiki-admin_links.php";
update tiki_menu_options set section="feature_hotwords" where url="tiki-admin_hotwords.php";
update tiki_menu_options set section="feature_polls" where url="tiki-admin_polls.php";
update tiki_menu_options set section="feature_search" where url="tiki-search_stats.php";
update tiki_menu_options set section="feature_chat" where url="tiki-admin_chat.php";
update tiki_menu_options set section="feature_categories" where url="tiki-admin_categories.php";
update tiki_menu_options set section="feature_edit_templates" where url="tiki-edit_templates.php";
update tiki_menu_options set section="feature_drawings" where url="tiki-admin_drawings.php";
update tiki_menu_options set section="feature_mailin" where url="tiki-admin_mailin.php";
update tiki_menu_options set section="feature_html_pages" where url="tiki-admin_html_pages.php";
update tiki_menu_options set section="feature_shoutbox" where url="tiki-shoutbox.php";
update tiki_menu_options set section="feature_shoutbox" where url="tiki-admin_shoutbox_words.php";
update tiki_menu_options set section="feature_referer_stats" where url="tiki-referer_stats.php";
update tiki_menu_options set section="feature_score" where url="tiki-admin_score.php";

# 2005-04-25 ohertel: switch for (dis/en)abling tiki-mobile
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_mobile', 'n');

-- From here on everything can be found in 1.9to1.9.x --

# 2005-04-28 get synchronised with tiki.sql
ALTER  TABLE  tiki_newsletter_groups  modify  code varchar(32) default NULL;

# 2005-04-29 rv540
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('trk_with_mirror_tables', 'n');

# 2005-05-03
UPDATE tiki_menu_options SET perm="tiki_p_view_trackers" WHERE url="tiki-list_trackers.php";

#2005-05-04 sg
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_view_tiki_calendar', 'Can view Tikiwiki tools calendar', 'basic', 'calendar');

# 2005-05-10 redflo
alter table tiki_sessions add tikihost varchar(200) default NULL;

#2005-06-08 sylvieg
UPDATE users_objectpermissions set permName='tiki_p_add_events' where permName='tiki_p_add_calendar';
UPDATE users_objectpermissions set permName='tiki_p_change_events' where permName='tiki_p_edit_calendar';

# 2005-06-14 rv540
alter table tiki_referer_stats change referer referer varchar(255) not null;

#2005-06-20 amette: added on request of toggg(currently without CVS-access)
ALTER TABLE `tiki_pages` ADD created int(14);
ALTER TABLE `tiki_cookies` CHANGE cookie cookie text;

CREATE TABLE `tiki_object_ratings` (
  `catObjectId` int(12) NOT NULL default '0',
  `pollId` int(12) NOT NULL default '0',
  PRIMARY KEY  (`catObjectId`,`pollId`)
) TYPE=MyISAM;

INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_bot_bar_icons', 'y');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_bot_bar_debug', 'y');

ALTER TABLE tiki_articles DROP COLUMN `bibliographical_references`;
ALTER TABLE tiki_articles DROP COLUMN `resume`;
#2005-07-28 gg: added preference for file gallery to have duplicates
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('fgal_allow_duplicates', 'n');

#2005-08-05 franck: New table to handle statistics per object
CREATE TABLE `tiki_stats` (
  `object` varchar(255) NOT NULL default '',
  `type` varchar(20) NOT NULL default '',
  `day` int(14) NOT NULL default '0',
  `hits` int(14) NOT NULL default '0',
  PRIMARY KEY  (`object`,`type`,`day`)
) TYPE=MyISAM;

# 2005-08-20 : fix by mose
update tiki_menu_options set section="feature_articles,feature_cms_rankings" where url="tiki-cms_rankings.php";

# 2005-08-22 : fix to get in sync with tiki.sql amette
ALTER TABLE `users_users` CHANGE score score int(11) NOT NULL default '0';

# 2005-08-27 : small speedup
create index `author` on `tiki_articles`(`author`(32));

# 2005-10-14: amette - Fixing broken Tracker rating, when user has not language of tracker-creator set
UPDATE tiki_tracker_fields SET name='Rating' WHERE type='s';

# 2005-10-16: mose - fixing user field length consistency to 40 chars (as in users_users)
ALTER TABLE `galaxia_instance_activities` CHANGE `user` `user` VARCHAR( 40 ) default NULL ;
ALTER TABLE `galaxia_instance_comments` CHANGE `user` `user` VARCHAR( 40 ) default NULL ;
ALTER TABLE `galaxia_user_roles` CHANGE `user` `user` VARCHAR( 40 ) NOT NULL default '' ;
ALTER TABLE `galaxia_workitems` CHANGE `user` `user` VARCHAR( 40 ) default NULL ;
ALTER TABLE `messu_messages` CHANGE `user` `user` VARCHAR( 40 ) NOT NULL default '' ;
ALTER TABLE `messu_archive` CHANGE `user` `user` VARCHAR( 40 ) NOT NULL default '' ;
ALTER TABLE `messu_archive` CHANGE `user_from` `user_from` VARCHAR( 40 ) NOT NULL default '';
ALTER TABLE `messu_sent` CHANGE `user` `user` VARCHAR( 40 ) NOT NULL default '' ;
ALTER TABLE `messu_sent` CHANGE `user_from` `user_from` VARCHAR( 40 ) NOT NULL default '' ;
ALTER TABLE `tiki_actionlog` CHANGE `user` `user` VARCHAR( 40 ) default NULL ;
ALTER TABLE `tiki_banning` CHANGE `user` `user` VARCHAR( 40 ) default NULL ;
ALTER TABLE `tiki_blog_posts` CHANGE `user` `user` VARCHAR( 40 ) default NULL ;
ALTER TABLE `tiki_blogs` CHANGE `user` `user` VARCHAR( 40 ) default NULL ;
ALTER TABLE `tiki_charts_votes` CHANGE `user` `user` VARCHAR( 40 ) NOT NULL default '' ;
ALTER TABLE `tiki_comments` CHANGE `userName` `userName` VARCHAR( 40 ) default NULL ;
ALTER TABLE `tiki_copyrights` CHANGE `userName` `userName` VARCHAR( 40 ) default NULL ;
ALTER TABLE `tiki_drawings` CHANGE `user` `user` VARCHAR( 40 ) default NULL ;
ALTER TABLE `tiki_file_galleries` CHANGE `user` `user` VARCHAR( 40 ) default NULL ;
ALTER TABLE `tiki_files` CHANGE `user` `user` VARCHAR( 40 ) default NULL ;
ALTER TABLE `tiki_forum_reads` CHANGE `user` `user` VARCHAR( 40 ) NOT NULL default '' ;
ALTER TABLE `tiki_forums_queue` CHANGE `user` `user` VARCHAR( 40 ) default NULL ;
ALTER TABLE `tiki_forums_reported` CHANGE `user` `user` VARCHAR( 40 ) default NULL ;
ALTER TABLE `tiki_galleries` CHANGE `user` `user` VARCHAR( 40 ) default NULL ;
ALTER TABLE `tiki_history` CHANGE `user` `user` VARCHAR( 40 ) default NULL ;
ALTER TABLE `tiki_images` CHANGE `user` `user` VARCHAR( 40 ) default NULL ;
ALTER TABLE `tiki_live_support_messages` CHANGE `user` `user` VARCHAR( 40 ) default NULL ;
ALTER TABLE `tiki_live_support_operators` CHANGE `user` `user` VARCHAR( 40 ) NOT NULL default '' ;
ALTER TABLE `tiki_live_support_requests` CHANGE `user` `user` VARCHAR( 40 ) default NULL ;
ALTER TABLE `tiki_logs` CHANGE `loguser` `loguser` VARCHAR( 40 ) NOT NULL ;
ALTER TABLE `tiki_mailin_accounts` CHANGE `user` `user` VARCHAR( 40 ) NOT NULL default '' ;
ALTER TABLE `tiki_minical_events` CHANGE `user` `user` VARCHAR( 40 ) default NULL ;
ALTER TABLE `tiki_minical_topics` CHANGE `user` `user` VARCHAR( 40 ) default NULL ;
ALTER TABLE `tiki_newsreader_marks` CHANGE `user` `user` VARCHAR( 40 ) NOT NULL default '' ;
ALTER TABLE `tiki_newsreader_servers` CHANGE `user` `user` VARCHAR( 40 ) NOT NULL default '' ;
ALTER TABLE `tiki_page_footnotes` CHANGE `user` `user` VARCHAR( 40 ) NOT NULL default '' ;
ALTER TABLE `tiki_pages` CHANGE `user` `user` VARCHAR( 40 ) default NULL ;
ALTER TABLE `tiki_semaphores` CHANGE `user` `user` VARCHAR( 40 ) default NULL ;
ALTER TABLE `tiki_sessions` CHANGE `user` `user` VARCHAR( 40 ) default NULL ;
ALTER TABLE `tiki_shoutbox` CHANGE `user` `user` VARCHAR( 40 ) default NULL ;
ALTER TABLE `tiki_suggested_faq_questions` CHANGE `user` `user` VARCHAR( 40 ) default NULL ;
ALTER TABLE `tiki_tags` CHANGE `user` `user` VARCHAR( 40 ) default NULL ;
ALTER TABLE `tiki_tracker_item_attachments` CHANGE `user` `user` VARCHAR( 40 ) default NULL ;
ALTER TABLE `tiki_tracker_item_comments` CHANGE `user` `user` VARCHAR( 40 ) default NULL ;
ALTER TABLE `tiki_user_assigned_modules` CHANGE `user` `user` VARCHAR( 40 ) NOT NULL default '' ;
ALTER TABLE `tiki_user_bookmarks_folders` CHANGE `user` `user` VARCHAR( 40 ) NOT NULL default '' ;
ALTER TABLE `tiki_user_bookmarks_urls` CHANGE `user` `user` VARCHAR( 40 ) NOT NULL default '' ;
ALTER TABLE `tiki_user_mail_accounts` CHANGE `user` `user` VARCHAR( 40 ) NOT NULL default '' ;
ALTER TABLE `tiki_user_menus` CHANGE `user` `user` VARCHAR( 40 ) NOT NULL default '' ;
ALTER TABLE `tiki_user_notes` CHANGE `user` `user` VARCHAR( 40 ) NOT NULL default '' ;
ALTER TABLE `tiki_user_postings` CHANGE `user` `user` VARCHAR( 40 ) NOT NULL default '' ;
ALTER TABLE `tiki_user_preferences` CHANGE `user` `user` VARCHAR( 40 ) NOT NULL default '' ;
ALTER TABLE `tiki_user_quizzes` CHANGE `user` `user` VARCHAR( 40 ) default NULL ;
ALTER TABLE `tiki_user_taken_quizzes` CHANGE `user` `user` VARCHAR( 40 ) NOT NULL default '' ;
ALTER TABLE `tiki_user_tasks` CHANGE `user` `user` VARCHAR( 40 ) NOT NULL default '' ;
ALTER TABLE `tiki_user_votings` CHANGE `user` `user` VARCHAR( 40 ) NOT NULL default '' ;
ALTER TABLE `tiki_user_watches` CHANGE `user` `user` VARCHAR( 40 ) NOT NULL default '' ;
ALTER TABLE `tiki_userfiles` CHANGE `user` `user` VARCHAR( 40 ) NOT NULL default '' ;
ALTER TABLE `tiki_userpoints` CHANGE `user` `user` VARCHAR( 40 ) default NULL ;
ALTER TABLE `tiki_webmail_contacts` CHANGE `user` `user` VARCHAR( 40 ) NOT NULL default '' ;
ALTER TABLE `tiki_webmail_messages` CHANGE `user` `user` VARCHAR( 40 ) NOT NULL default '' ;
ALTER TABLE `tiki_wiki_attachments` CHANGE `user` `user` VARCHAR( 40 ) default NULL ;
ALTER TABLE `tiki_users` CHANGE `user` `user` VARCHAR( 40 ) NOT NULL DEFAULT '' ;

# 2005-10-16: mose - fixing indexes too long, spitting error on mysql 4.1
ALTER TABLE `tiki_newsreader_marks` DROP PRIMARY KEY , ADD PRIMARY KEY ( `user` , `serverId` , `groupName` ( 100 ) ) ;
ALTER TABLE `tiki_page_footnotes` DROP PRIMARY KEY , ADD PRIMARY KEY ( `user` , `pageName` ( 100 ) ) ;
ALTER TABLE `tiki_searchindex` DROP PRIMARY KEY , ADD PRIMARY KEY ( `searchword` , `location` , `page` ( 80 ) ) ;
ALTER TABLE `tiki_secdb` DROP PRIMARY KEY , ADD PRIMARY KEY ( `md5_value` , `filename` ( 100 ) , `tiki_version` ) ;
ALTER TABLE `tiki_user_taken_quizzes` DROP PRIMARY KEY , ADD PRIMARY KEY ( `user` , `quizId` ( 100 ) ) ;
ALTER TABLE `tiki_user_votings` DROP PRIMARY KEY , ADD PRIMARY KEY ( `user` , `id` ( 100 ) ) ;
ALTER TABLE `tiki_user_watches` DROP PRIMARY KEY , ADD PRIMARY KEY ( `user` , `event` , `object` ( 100 ) ) ;

# 2005-10-30: ohertel - added Tiki Mobile to the menu
INSERT IGNORE INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Mobile','tiki-mobile.php',37,'feature_mobile','','');

# not needed anymore, so drop it:
# NO ! NEVER put a 'DROP' in this file (except maybe indexes). People may have used it
#DROP TABLE IF EXISTS tiki_features;
#DROP TABLE IF EXISTS users_score;
ALTER TABLE users_users DROP KEY score_2;
ALTER TABLE users_groups DROP groupHomeLocalized;

# missing field in primary key:
ALTER TABLE `users_objectpermissions` DROP PRIMARY KEY , ADD PRIMARY KEY ( `objectId` , `objectType` , `groupName` ( 30 ), `permName` ) ;

# 2005-05-03 - amette - correct perm for submitting link - WYSIWYCA
UPDATE tiki_menu_options SET perm="tiki_p_submit_link" WHERE url="tiki-directory_add_site.php";

# 2006-04-13 fixing Typo - amette
UPDATE `users_permissions` SET `permDesc`='Can create user bookmarks' WHERE `permName`='tiki_p_create_bookmarks';

# 2006-04-26 security issue, split template edit perm into edit and view perms
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_view_templates', 'Can view site templates', 'admin', 'tiki');

# fixed reserved word use in mysql
ALTER TABLE `tiki_articles` change `reads` `nbreads` int(14) default NULL ;
ALTER TABLE `tiki_submissions` change `reads` `nbreads` int(14) default NULL ;
ALTER TABLE `tiki_articles` DROP INDEX `reads`, ADD INDEX `nbreads` ( `nbreads` ) ;
ALTER TABLE `tiki_submissions` DROP INDEX `reads`, ADD INDEX `nbreads` ( `nbreads` ) ;

# 2006-05-26 new preference eq 'y' to keep the default - sampaioprimo
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_cms_print','y');

# 2006-07-05 new preferences eq 'y' to keep the default - toggg
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_trackbackpings','y');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_blogposts_pings','y');

# 2006-07-31 slideshow default enabled - toggg
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_gal_slideshow','y');

#2006-08-14 sylvieg
ALTER TABLE tiki_tracker_item_fields ADD FULLTEXT ft (value);

#2006-09-03 ohertel - missing on 1.8+update vs 1.9 diff
INSERT INTO tiki_modules VALUES ('quick_edit','l',2,NULL,NULL,0,NULL,NULL,'a:1:{i:0;s:10:\"Registered\";}');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_help','n');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_multilingual','n');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_wiki_export','n');
ALTER TABLE `tiki_quizzes` DROP PRIMARY KEY , ADD PRIMARY KEY ( `quizId` , `nVersion` );
ALTER TABLE `tiki_trackers` ADD showAttachments char(1) default NULL AFTER useAttachments;

# 2006-10-21 mose/cfreeze - changed pear auth params to be more generic
update tiki_preferences set name='auth_pear_host' where name='auth_ldap_host';
update tiki_preferences set name='auth_pear_port' where name='auth_ldap_port';

#sylvieg 2006-12-08
ALTER TABLE tiki_categorized_objects ADD KEY(type, objId);

#sylvieg 2007-03-23
ALTER TABLE `tiki_links` ADD INDEX `toPage` (`toPage`);

# 2006-06-09 nkoth - new preference eq 'y' to keep the default
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_wiki_import_page','y');

#sylvieg
ALTER TABLE tiki_pages CHANGE `cache` `cache` longtext;

# 2007-07-06 xavidp - menu option inserted for users map (Google maps) when gmap enabled
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Users map','tiki-gmap_usermap.php',36,'feature_gmap','','');

#2007-07-10 sylvieg - thx tibi
ALTER TABLE users_permissions ADD INDEX type (type);
ALTER TABLE tiki_articles ADD INDEX topicId (topicId);
ALTER TABLE tiki_articles ADD INDEX publishDate (publishDate);
ALTER TABLE tiki_articles ADD INDEX expireDate (expireDate);
ALTER TABLE tiki_articles ADD INDEX type (type);
ALTER TABLE tiki_forum_attachments ADD INDEX threadId (threadId);
ALTER TABLE users_users ADD INDEX login (login);
ALTER TABLE tiki_article_types ADD INDEX show_pre_publ (show_pre_publ);
ALTER TABLE tiki_article_types ADD INDEX show_post_expire (show_post_expire);
ALTER TABLE tiki_comments DROP KEY object;
ALTER TABLE tiki_comments ADD INDEX objectType (object, objectType);
ALTER TABLE tiki_comments ADD INDEX commentDate (commentDate);
ALTER TABLE tiki_sessions ADD INDEX user (user);
ALTER TABLE tiki_sessions ADD INDEX timestamp (timestamp);
ALTER TABLE tiki_galleries ADD INDEX parentgallery (parentgallery);
ALTER TABLE tiki_galleries ADD INDEX visibleUser (visible, user);
ALTER TABLE tiki_modules ADD INDEX positionType (position, type);
ALTER TABLE tiki_link_cache ADD INDEX url(url);
ALTER TABLE messu_messages ADD INDEX  userIsRead (user, isRead);

#2007-07-18
ALTER TABLE users_users ADD valid varchar(32) default NULL;

# 2007-08-25 marclaporte "Password invalid after days" has been always on by default and at 999 days. 
# While this seems like a long time, many sites are reaching that age and users are being asked to change 
# their password. This process is not vert good at the moment. So, I'm going to assume that the vast majority 
# of people left the default but don't want this feature. So the upgrade script will change to 1999 days. 
# And clean installs will be at 1999 days as well.  In 1.10, the default will be that this feature is turned off (-1)
UPDATE tiki_preferences SET value = '1999' WHERE value='999' and name='pass_due';

# 2007-09-12 marclaporte: following on Luciash's recent fixes on tiki.sql
# This alters db column 'value' of tiki_preferences to support more than 255 characters (up to 65535 actually) 
# which is essential to do anything significative with Site Identity (and avoid getting locked out of Tiki by accident)
# It is also used if you have many entries in InterTiki.
ALTER TABLE tiki_preferences CHANGE `value` `value` text;
