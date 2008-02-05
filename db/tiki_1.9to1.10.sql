# $Header: /cvsroot/tikiwiki/tiki/db/tiki_1.9to1.10.sql,v 1.221.2.23 2008-02-05 18:36:32 lphuberdeau Exp $

# The following script will update a tiki database from version 1.9 to 1.10
# 
# To execute this file do the following:
#
# $ mysql -f dbname < tiki_1.9to1.10.sql
#
# where dbname is the name of your tiki database.
#
# For example, if your tiki database is named tiki (not a bad choice), type:
#
# $ mysql -f tiki < tiki_1.9to1.10.sql
# 
# You may execute this command as often as you like, 
# and may safely ignore any error messages that appear.

INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('pear_wiki_parser','n');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_mootools','n');

#2005-06-22 rlpowell: available_languages was getting truncated if all languages were selected
ALTER TABLE `tiki_preferences` CHANGE value value text;

#2005-07-15 rlpowell: Had a wiki page get truncated! Very annoying.
# This will allow up to 16777216 bytes instead of 65536
ALTER TABLE `tiki_pages` CHANGE data data mediumtext;
ALTER TABLE `tiki_pages` CHANGE cache cache mediumtext;

# 2005-07-19 rv540 : users defaults used when creating a new user account
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('users_prefs_theme', 'global');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('users_prefs_userbreadCrumb', '4');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('users_prefs_language', 'global');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('users_prefs_display_timezone', 'Local');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('users_prefs_user_information', 'private');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('users_prefs_user_dbl', 'n');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('users_prefs_diff_versions', 'n');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('users_prefs_show_mouseover_user_info', 'n');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('users_prefs_email_is_public', 'n');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('users_prefs_mailCharset', 'utf-8');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('users_prefs_realName', '');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('users_prefs_homePage', '');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('users_prefs_lat', '0');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('users_prefs_lon', '0');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('users_prefs_country', '');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('users_prefs_mess_maxRecords', '10');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('users_prefs_mess_archiveAfter', '0');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('users_prefs_mess_sendReadStatus', 'n');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('users_prefs_minPrio', '6');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('users_prefs_allowMsgs', 'n');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('users_prefs_mytiki_pages', 'n');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('users_prefs_mytiki_blogs', 'n');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('users_prefs_mytiki_gals', 'n');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('users_prefs_mytiki_msgs', 'n');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('users_prefs_mytiki_tasks', 'n');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('users_prefs_mytiki_items', 'n');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('users_prefs_mytiki_workflow', 'n');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('users_prefs_tasks_maxRecords', '10');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_copyright', '10');


# 2005-08-26 / 2005-09-31: mdavey: new table tiki_events for notificationlib / tikisignal
CREATE TABLE `tiki_events` (
  `callback_type` int(1) NOT NULL default '3',
  `order` int(2) NOT NULL default '50',
  `event` varchar(200) NOT NULL default '',
  `file` varchar(200) NOT NULL default '',
  `object` varchar(200) NOT NULL default '',
  `method` varchar(200) NOT NULL default '',
  PRIMARY KEY  (`callback_type`,`order`)
) ENGINE=MyISAM;

# 2005-09-31: mdavey: make sure developers are using the 6-column version of tiki_events
ALTER TABLE `tiki_events` ADD `file` varchar(200) NOT NULL default '' AFTER `event`;

# 2005-08-26 / 2005-09-31: mdavey: new table tiki_events for notificationlib / tikisignal
INSERT IGNORE INTO tiki_events(`callback_type`,`order`,`event`,`file`,`object`,`method`) VALUES ('1', '20', 'user_registers', 'lib/registration/registrationlib.php', 'registrationlib', 'callback_tikiwiki_setup_custom_fields');
INSERT IGNORE INTO tiki_events(`event`,`file`,`object`,`method`) VALUES ('user_registers', 'lib/registration/registrationlib.php', 'registrationlib', 'callback_tikiwiki_save_registration');
INSERT IGNORE INTO tiki_events(`callback_type`,`order`,`event`,`file`,`object`,`method`) VALUES ('5', '20', 'user_registers', 'lib/registration/registrationlib.php', 'registrationlib', 'callback_logslib_user_registers');
INSERT IGNORE INTO tiki_events(`callback_type`,`order`,`event`,`file`,`object`,`method`) VALUES ('5', '25', 'user_registers', 'lib/registration/registrationlib.php', 'registrationlib', 'callback_tikiwiki_send_email');
INSERT IGNORE INTO tiki_events(`callback_type`,`order`,`event`,`file`,`object`,`method`) VALUES ('5', '30', 'user_registers', 'lib/registration/registrationlib.php', 'registrationlib', 'callback_tikimail_user_registers');

# 2005-09-31: mdavey: make sure developers are using the 6-column version of tiki_events
UPDATE `tiki_events` SET `file` = 'lib/registration/registrationlib.php' WHERE `callback_type` = '1' AND `order` = '20';
UPDATE `tiki_events` SET `file` = 'lib/registration/registrationlib.php' WHERE `callback_type` = '3' AND `order` = '50';
UPDATE `tiki_events` SET `file` = 'lib/registration/registrationlib.php' WHERE `callback_type` = '5' AND `order` = '20';
UPDATE `tiki_events` SET `file` = 'lib/registration/registrationlib.php' WHERE `callback_type` = '5' AND `order` = '25';
UPDATE `tiki_events` SET `file` = 'lib/registration/registrationlib.php' WHERE `callback_type` = '5' AND `order` = '30';

# 2005-08-31: mdavey: new table tiki_registration_fields
CREATE TABLE `tiki_registration_fields` (
  `id` int(11) NOT NULL auto_increment,
  `field` varchar(255) NOT NULL default '',
  `name` varchar(255) default NULL,
  `type` varchar(255) NOT NULL default 'text',
  `show` tinyint(1) NOT NULL default '1',
  `size` varchar(10) default '10',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

# 2005-09-22: mdavey: move custom fields to new table
INSERT IGNORE INTO `tiki_registration_fields` (field, name) SELECT prefName as field, value as name FROM `tiki_user_preferences` WHERE user='CustomFields';
DELETE FROM  `tiki_user_preferences` WHERE user='CustomFields';

# 2005-09-07: rlpowell: These changes make a *huge* difference to speed of retrieval of forum threads.
ALTER TABLE tiki_comments MODIFY COLUMN `message_id` varchar(128) default NULL;
ALTER TABLE tiki_comments MODIFY COLUMN `in_reply_to` varchar(128) default NULL;
ALTER TABLE tiki_comments ADD INDEX THREADED (message_id, in_reply_to, parentId);

# 2005-09-07: rlpowell: These changes stop the mail system from repeatedly adding the same posts.
ALTER TABLE tiki_comments MODIFY COLUMN `userName` varchar(40) default NULL;
ALTER IGNORE TABLE tiki_comments ADD UNIQUE parentId(parentId, userName, title, commentDate, message_id, in_reply_to);
# NOTE: It is possible to lose data with the "ALTER IGNORE TABLE" line, but it should only be repeat data anyways.
# In addition, ALTER IGNORE TABLE is a MySQL extension.  If it doesn't work,
# the following should give you a tiki_comments table that you can apply the unique key to, but I suggest
# making a copy first.
# delete from tiki_comments tc1, tiki_comments tc2 where tc1.threadId < tc2.threadId and tc1.parentId = tc2.parentId and  tc1.userName = tc2.userName and  tc1.title = tc2.title and  tc1.commentDate = tc2.commentDate and  tc1.message_id = tc2.message_id and tc1.in_reply_to = tc2.in_reply_to;

# 2005-09-08 sylvieg
INSERT IGNORE INTO `tiki_preferences`(`name`,`value`) VALUES ('feature_wiki_protect_email', 'n');
INSERT IGNORE INTO tiki_preferences(`name`,`value`) VALUES ('feature_wiki_1like_redirection', 'y');

# 2005-09-12 sylvieg
ALTER TABLE `tiki_actionlog` CHANGE `pageName` `object` varchar(255) default NULL;
ALTER TABLE `tiki_actionlog` ADD `objectType` varchar(32) NOT NULL default '' AFTER `object`;
ALTER TABLE `tiki_actionlog` ADD `categId` int(12) NOT NULL default '0' AFTER `comment`;
ALTER TABLE `tiki_actionlog` ADD `actionId` int(8) NOT NULL auto_increment FIRST, ADD PRIMARY KEY (`actionId`);
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_actionlog', 'y');
DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='o' and name='Action Log' and url='tiki-admin_actionlog.php' and position='1255' and section='feature_actionlog' and perm='tiki_p_admin' and groupname='' ;
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Action Log','tiki-admin_actionlog.php',1255,'feature_actionlog','tiki_p_admin','');
CREATE TABLE `tiki_actionlog_conf` (
 `action` varchar(32) NOT NULL default '',
 `objectType`varchar(32) NOT NULL default '',
 `status` char(1) default '',
 PRIMARY KEY (`action`, `objectType`)
) ENGINE=MyISAM;
INSERT IGNORE INTO `tiki_actionlog_conf`(`action`, `objectType`, `status`) VALUES ('Created', 'wiki page', 'y');
INSERT IGNORE INTO `tiki_actionlog_conf`(`action`, `objectType`, `status`) VALUES ('Updated', 'wiki page', 'y');
INSERT IGNORE INTO `tiki_actionlog_conf`(`action`, `objectType`, `status`) VALUES ('Removed', 'wiki page', 'y');
INSERT IGNORE INTO `tiki_actionlog_conf`(`action`, `objectType`, `status`) VALUES ('Viewed', 'wiki page', 'n');
INSERT IGNORE INTO `tiki_actionlog_conf`(`action`, `objectType`, `status`) VALUES ('Viewed', 'forum', 'n');
INSERT IGNORE INTO `tiki_actionlog_conf`(`action`, `objectType`, `status`) VALUES ('Posted', 'forum', 'n');
INSERT IGNORE INTO `tiki_actionlog_conf`(`action`, `objectType`, `status`) VALUES ('Replied', 'forum', 'n');
INSERT IGNORE INTO `tiki_actionlog_conf`(`action`, `objectType`, `status`) VALUES ('Updated', 'forum', 'n');
INSERT IGNORE INTO `tiki_actionlog_conf`(`action`, `objectType`, `status`) VALUES ('Viewed', 'file gallery', 'n');
INSERT IGNORE INTO `tiki_actionlog_conf`(`action`, `objectType`, `status`) VALUES ('Viewed', 'image gallery', 'n');
INSERT IGNORE INTO `tiki_actionlog_conf`(`action`, `objectType`, `status`) VALUES ('Uploaded', 'file gallery', 'n');
INSERT IGNORE INTO `tiki_actionlog_conf`(`action`, `objectType`, `status`) VALUES ('Uploaded', 'image gallery', 'n');
INSERT IGNORE INTO `tiki_actionlog_conf`(`action`, `objectType`, `status`) VALUES ('*', 'category', 'n');
INSERT IGNORE INTO `tiki_actionlog_conf`(`action`, `objectType`, `status`) VALUES ('*', 'login', 'n');
INSERT IGNORE INTO `tiki_actionlog_conf`(`action`, `objectType`, `status`) VALUES ('Posted', 'message', 'n');
INSERT IGNORE INTO `tiki_actionlog_conf`(`action`, `objectType`, `status`) VALUES ('Replied', 'message', 'n');
INSERT IGNORE INTO `tiki_actionlog_conf`(`action`, `objectType`, `status`) VALUES ('Viewed', 'message', 'n');
INSERT IGNORE INTO `tiki_actionlog_conf`(`action`, `objectType`, `status`) VALUES ('Removed version', 'wiki page', 'n');
INSERT IGNORE INTO `tiki_actionlog_conf`(`action`, `objectType`, `status`) VALUES ('Removed last version', 'wiki page', 'n');
INSERT IGNORE INTO `tiki_actionlog_conf`(`action`, `objectType`, `status`) VALUES ('Rollback', 'wiki page', 'n');

#2005-09-21 sylvieg
INSERT IGNORE INTO tiki_preferences(`name`,`value`) VALUES ('feature_wiki_show_hide_before', 'n');
# --------------------------------------------------------

#2005-09-27 brazilian tiki study group
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_batch_subscribe_email', 'Can subscribe many e-mails at once (requires tiki_p_subscribe email)', 'editors', 'newsletters');

#2005-10-04 sylvieg
DELETE FROM tiki_logs where logmessage='timeout' and loguser='Anonymous';

#2005-10-21 sylvieg
INSERT INTO `tiki_actionlog_conf`(`action`, `objectType`, `status`) VALUES ('Removed', 'forum', 'n');
CREATE INDEX lastModif on tiki_pages (lastModif);

#2005-10-24 sylvieg to boost tiki_stats and tiki_orphan
CREATE INDEX toPage on tiki_links (toPage);
CREATE INDEX page_id on tiki_structures (page_id);

#2005-10-25 sylvieg sped up refresh index
CREATE INDEX locationPage on tiki_searchindex (location, page);

#2005-10-26 sylvieg
INSERT IGNORE INTO `tiki_actionlog_conf`(`action`, `objectType`, `status`) VALUES ('Downloaded', 'file gallery', 'n');

#2005-11-07 sylvieg
INSERT IGNORE INTO `tiki_actionlog_conf`(`action`, `objectType`, `status`) VALUES ('Posted', 'comment', 'n');
INSERT IGNORE INTO `tiki_actionlog_conf`(`action`, `objectType`, `status`) VALUES ('Replied', 'comment', 'n');
INSERT IGNORE INTO `tiki_actionlog_conf`(`action`, `objectType`, `status`) VALUES ('Updated', 'comment', 'n');
INSERT IGNORE INTO `tiki_actionlog_conf`(`action`, `objectType`, `status`) VALUES ('Removed', 'comment', 'n');

#2005-11-09 sylvieg
DELETE FROM tiki_preferences WHERE name='users_prefs_language';
DELETE FROM tiki_preferences WHERE name='users_prefs_theme';
DELETE FROM tiki_preferences WHERE name='users_prefs_mailCharset';
DELETE FROM tiki_user_preferences WHERE prefName='users_prefs_language' and value='global';
DELETE FROM tiki_user_preferences WHERE prefName='users_prefs_theme' and value='global';
DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='o' and name='Comments' and url='tiki-list_comments.php' and position='1260' and perm='tiki_p_admin' and groupname='' ;
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Comments','tiki-list_comments.php',1260,'feature_wiki_comments','tiki_p_admin','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Comments','tiki-list_comments.php',1260,'feature_article_comments','tiki_p_admin','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Comments','tiki-list_comments.php',1260,'feature_blog_comments','tiki_p_admin','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Comments','tiki-list_comments.php',1260,'feature_file_galleries_comments','tiki_p_admin','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Comments','tiki-list_comments.php',1260,'feature_image_galleries_comments','tiki_p_admin','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Comments','tiki-list_comments.php',1260,'feature_poll_comments','tiki_p_admin','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Comments','tiki-list_comments.php',1260,'feature_faq_comments','tiki_p_admin','');

#2005-11-14 sylvieg
CREATE INDEX positionType ON tiki_modules (position, type);
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_homePage_if_bl_missing', 'n');

#2005-12-02 amette

CREATE TABLE `tiki_freetags` (
  `tagId` int(10) unsigned NOT NULL auto_increment,
  `tag` varchar(30) NOT NULL default '',
  `raw_tag` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`tagId`)
) ENGINE=MyISAM;

#2005-12-06 lfagundes

ALTER TABLE `tiki_categorized_objects` rename to `tiki_objects`;
ALTER TABLE `tiki_objects` CHANGE `catObjectId` `objectId` int(12) not null auto_increment;
ALTER TABLE `tiki_objects` CHANGE `objId` `itemId` varchar(255);

CREATE TABLE `tiki_freetagged_objects` (
  `tagId` int(12) NOT NULL auto_increment,
  `objectId` int(11) NOT NULL default 0,
  `user` varchar(40) NOT NULL default '',
  `created` int(14) NOT NULL default '0',
  PRIMARY KEY  (`tagId`,`user`,`objectId`),
  KEY (`tagId`),
  KEY (`user`),
  KEY (`objectId`)
) ENGINE=MyISAM;

#2005-12-07 lfagundes

CREATE TABLE `tiki_categorized_objects` (
  `catObjectId` int(11) NOT NULL default '0',
  PRIMARY KEY  (`catObjectId`)
) ENGINE=MyISAM ;

#2005-12-09 lfagundes

INSERT INTO `tiki_categorized_objects` SELECT `objectId` FROM `tiki_objects`;

#2005-12-12 sylvieg
ALTER TABLE users_groups ADD registrationChoice CHAR(1) DEFAULT NULL;
CREATE INDEX login ON users_users (login);

#2005-12-15 amette
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_ajax','n');

#2005-12-15 amette - Freetag permissions and menu item
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_view_freetags', 'Can browse freetags', 'basic', 'freetags');
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_freetags_tag', 'Can tag objects', 'registered', 'freetags');
DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='o' and name='Freetags' and url='tiki-browse_freetags.php' and position='27' and perm='tiki_p_view_freetags' and groupname='' ;
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Freetags','tiki-browse_freetags.php',27,'feature_freetags','tiki_p_view_freetags','');

#2005-12-16 lfagundes
ALTER TABLE `tiki_history` ADD KEY user (`user`);

#2006-01-05 sg
ALTER TABLE users_groups ADD registrationUsersFieldIds text;
ALTER TABLE tiki_tracker_fields ADD description text;

INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_wiki_mandatory_category',-1);
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_blog_mandatory_category',-1);
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_image_gallery_mandatory_category',-1);
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_display_my_to_others', 'n');

#2006-02-11 lfagundes
alter table tiki_private_messages add `received` tinyint(1) not null default 0;
alter table tiki_private_messages add key received (`received`); 
alter table tiki_private_messages add key timestamp (`timestamp`); 

# "data" is reserved word in cpaint
alter table tiki_private_messages add `message` varchar(255);
update tiki_private_messages set `message`=`data`;
alter table `tiki_private_messages` drop `data`;

# sylvieg 3/2/06 & Jyhem 2007-06-14
CREATE TABLE tiki_contributions (
  contributionId int(12) NOT NULL auto_increment,
  name varchar(100) default NULL,
  description varchar(250) default NULL,
  PRIMARY KEY  (contributionId)
) ENGINE=MyISAM AUTO_INCREMENT=1;

CREATE TABLE tiki_contributions_assigned (
  contributionId int(12) NOT NULL,
  objectId int(12) NOT NULL,
  PRIMARY KEY  (objectId, contributionId)
) ENGINE=MyISAM;

INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_contribution', 'n');
DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='r' and name='Admin' and url='tiki-admin.php' and position='1050' and section='' and perm='tiki_p_admin_contribution' and groupname='' ;
INSERT INTO `tiki_menu_options` (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_admin_contribution','');
DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='o' and name='Contribution' and url='tiki-admin_contribution.php' and position='1265' and section='feature_contribution' and perm='tiki_p_admin_contribution' and groupname='' ;
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Contribution','tiki-admin_contribution.php',1265,'feature_contribution','tiki_p_admin_contribution','');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_contribution_mandatory', 'n');
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_admin_contribution', 'Can admin contributions', 'admin', 'contribution');
ALTER TABLE `tiki_history` ADD `historyId` int(12) NOT NULL auto_increment FIRST, ADD  KEY (`historyId`);
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_contribution_display_in_comment', 'y');

INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_contribution_mandatory_forum', 'n');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_contribution_mandatory_comment', 'n');

#2006-03-12 lfagundes
CREATE TABLE tiki_page_drafts (
  user varchar(40) NOT NULL,
  pageName varchar(255) NOT NULL,
  data mediumtext,
  description varchar(200) default NULL,
  comment varchar(200) default NULL,
  PRIMARY KEY  (pageName, user)
) ENGINE=MyISAM;

#2006-03-16 sampaioprimo
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('calendar_view_mode','week');

#2006-03-19 lfagundes
alter table `tiki_page_drafts` add `lastModif` int(14); 

#2006-03-28
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_wiki_replace', 'n');

#2006-03-30 sylvieg
UPDATE tiki_menu_options SET perm='tiki_p_view_sheet' where url='tiki-sheets.php';
CREATE TABLE tiki_actionlog_params (
  actionId int(8) NOT NULL,

  name varchar(40) NOT NULL,
  value text,
  KEY  (actionId)
) ENGINE=MyISAM;
#2006-04-06
INSERT IGNORE INTO `tiki_actionlog_conf`(`action`, `objectType`, `status`) VALUES ('Renamed', 'wiki page', 'n');
#2006-04-11
DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='o' and name='List TikiSheets' and url='tiki-sheets.php' and position='782' and section='feature_sheet' and perm='tiki_p_view_sheet' and groupname='' ;
INSERT IGNORE INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','List TikiSheets','tiki-sheets.php',782,'feature_sheet','tiki_p_view_sheet','');
INSERT IGNORE INTO `tiki_actionlog_conf`(`action`, `objectType`, `status`) VALUES ('Created', 'sheet', 'n');
INSERT IGNORE INTO `tiki_actionlog_conf`(`action`, `objectType`, `status`) VALUES ('Updated', 'sheet', 'n');
INSERT IGNORE INTO `tiki_actionlog_conf`(`action`, `objectType`, `status`) VALUES ('Removed', 'sheet', 'n');
INSERT IGNORE INTO `tiki_actionlog_conf`(`action`, `objectType`, `status`) VALUES ('Viewed', 'sheet', 'n');
ALTER TABLE `tiki_sheet_values` ADD `user` varchar(40) NULL default '' AFTER `format`;
#2006-04-25
CREATE TABLE tiki_sent_newsletters_errors (
  editionId int(12),
  email varchar(255),
  login varchar(40) default '',
  error char(1) default '',
  KEY  (editionId)
) ENGINE=MyISAM ;
#2006-04-27
ALTER TABLE `tiki_semaphores` ADD `objectType` varchar(20) default 'wiki page' AFTER `semName`;

#2006-04-29 sampaioprimo
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('min_username_length','1');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('max_username_length','50');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('lowercase_username','n');

#2006-05-25 sampaioprimo & Jyhem 2007-06-14
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_wiki_view_source', 'Can view source of wiki pages', 'basic', 'wiki');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_source','y');

# 2006-05-26 new preference eq 'y' to keep the default - sampaioprimo
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_cms_print','y');

# 2006-06-07 sylvieg (merge from 1.9)
insert into users_permissions (permName,permDesc,level,type) values ('tiki_p_admin_objects','Can edit object permissions', 'admin', 'tiki');
insert into users_permissions (permName,permDesc,level,type) values ('tiki_p_admin_polls','Can admin polls', 'admin', 'tiki');
INSERT INTO users_permissions (permName,permDesc,level,type) values ('tiki_p_admin_rssmodules','Can admin rss modules', 'admin', 'tiki');

ALTER TABLE users_users MODIFY COLUMN `hash` varchar(34) default NULL;
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_crypt_passwords','tikihash');
#2006-07-28 mkalbere
ALTER TABLE `tiki_sent_newsletters` ADD `datatxt` longblob AFTER data;
ALTER TABLE `tiki_newsletters` ADD `allowTxt` varchar(1);

#sylvieg 9/13/06
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_contribution_mandatory_blog', 'n');
INSERT IGNORE INTO `tiki_actionlog_conf`(`action`, `objectType`, `status`) VALUES ('Viewed', 'blog', 'n');
INSERT IGNORE INTO `tiki_actionlog_conf`(`action`, `objectType`, `status`) VALUES ('Posted', 'blog', 'n');
INSERT IGNORE INTO `tiki_actionlog_conf`(`action`, `objectType`, `status`) VALUES ('Updated', 'blog', 'n');
INSERT IGNORE INTO `tiki_actionlog_conf`(`action`, `objectType`, `status`) VALUES ('Removed', 'blog', 'n');

#ohertel 9/20/06 - type, required for special fgals (podcasts)
ALTER TABLE `tiki_file_galleries` ADD `type` varchar(20) NOT NULL default 'default' AFTER `name`;

#ohertel 09/23/06 adding Directory Batch Load feature for File Galleries
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_batch_upload_file_dir', 'Can use Directory Batch Load', 'editors', 'file galleries');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_file_galleries_batch','n');
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('fgal_batch_dir','');

#sylvieg 10/27/06 (delete not null for batch use)
ALTER TABLE tiki_logs CHANGE logip logip varchar(200);

#sylvieg 11/3/06
INSERT INTO users_grouppermissions(groupName,permName) values ('Registered','tiki_p_watch_trackers');
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_watch_trackers', 'Can watch tracker', 'Registered', 'trackers');

#sylvieg 11/6/6
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('fgal_list_type','n');

# 2006-10-21 mose/cfreeze - changed pear auth params to be more generic
update tiki_preferences set name='auth_pear_host' where name='auth_ldap_host';
update tiki_preferences set name='auth_pear_port' where name='auth_ldap_port';

#sylvieg 2006-11-13
ALTER TABLE `tiki_file_galleries` ADD `parentId` int(14) NOT NULL default -1;
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('fgal_list_parent','n');

#sylvieg 2006-11-16 & Jyhem 2007-06-14
ALTER TABLE `tiki_file_galleries` ADD `lockable` char(1) default 'n';
ALTER TABLE `tiki_file_galleries` ADD `show_lockedby` char(1) default NULL;
ALTER TABLE `tiki_files` ADD `lockedby`  varchar(40) default NULL;
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_edit_gallery_file', 'Can edit a gallery file', 'editors', 'file galleries');
DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='o' and name='Directory batch' and url='tiki-batch_upload_files.php' and position='617' and section='feature_file_galleries_batch' and perm='tiki_p_batch_upload_file_dir' and groupname='' ;
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Directory batch','tiki-batch_upload_files.php',617,'feature_file_galleries_batch','tiki_p_batch_upload_file_dir','');

#mkalbere 2006-11-23 - To have multilingual tracker fields
ALTER TABLE `tiki_tracker_fields` ADD `isMultilingual` char(1) default 'n';
ALTER TABLE `tiki_tracker_item_fields` ADD `lang` char(16) default NULL;
ALTER TABLE `tiki_tracker_item_fields` DROP PRIMARY KEY;
ALTER TABLE `tiki_tracker_item_fields` ADD PRIMARY KEY  (itemId,fieldId,lang);
#sylvieg 2006-11-21
ALTER TABLE `tiki_file_galleries` ADD `archives` int(4) default -1;
ALTER TABLE `tiki_files` ADD `comment` varchar(200) default NULL;
ALTER TABLE `tiki_files` ADD `archiveId` int(14) default 0;

#lmoss 2006-11-28 - Increase article title length to 255
ALTER TABLE `tiki_articles` CHANGE `title` `title` varchar(255) default NULL;
ALTER TABLE `tiki_submissions` CHANGE `title` `title` varchar(255) default NULL;

# mose 2006-11-28 - new user contacts menu entry & Jyhem 2007-06-14
DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='o' and name='Contacts' and url='tiki-contacts.php' and position='87' and (section='feature_contacts' or section='feature_mytiki,feature_contacts') and perm='' and groupname='Registered' ;
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Contacts','tiki-contacts.php',87,'feature_contacts','','Registered');

# mose 2006-11-28 - new contacts groups feature
CREATE TABLE tiki_webmail_contacts_groups (
  contactId int(12) NOT NULL,
  groupName varchar(255) NOT NULL,
  PRIMARY KEY  (contactId,groupName(200))
) ENGINE=MyISAM ;

#sylvieg 2006-11-30
ALTER TABLE `users_grouppermissions` CHANGE `permName` `permName` varchar(31)  NOT NULL default '';
ALTER TABLE `users_objectpermissions` CHANGE `permName` `permName` varchar(31)  NOT NULL default '';
ALTER TABLE `users_permissions` CHANGE `permName` `permName` varchar(31)  NOT NULL default '';
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_assign_perm_file_gallery', 'Can assign perms to file gallery', 'admin', 'file galleries');

#sylvieg 2006-12-01
ALTER TABLE `tiki_file_galleries` ADD `sort_mode` char(20) default NULL;

# mose 2006-12-03 & Jyhem 2007-06-14
CREATE TABLE tiki_calendar_options (
	calendarId int(14) NOT NULL default 0,
	optionName varchar(120) NOT NULL default '',
	value varchar(255),
	PRIMARY KEY (calendarId,optionName)
) ENGINE=MyISAM ;

update `users_permissions` set type='tiki' where `permName`='tiki_p_view_tiki_calendar' and `type`='calendar';
DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='o' and name='Tiki Calendar' and url='tiki-action_calendar.php' and position='36' and section='feature_action_calendar' and perm='tiki_p_view_tiki_calendar' and groupname='' ;
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Tiki Calendar','tiki-action_calendar.php',36,'feature_action_calendar','tiki_p_view_tiki_calendar','');

# mose 2006-12-05
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_view_events', 'Can view events details', 'registered', 'calendar');

#sylvieg 2006-12-08
ALTER TABLE tiki_objects ADD KEY type (type, objectId);
ALTER TABLE `tiki_file_galleries` ADD `show_modified` char(1) default NULL;

#sylvieg 2006-12-12
ALTER TABLE tiki_files ADD author varchar(40) default NULL AFTER user;
ALTER TABLE `tiki_file_galleries` ADD `show_author` char(1) default NULL;
ALTER TABLE `tiki_file_galleries` ADD `show_creator` char(1) default NULL;
UPDATE tiki_file_galleries SET show_creator='y' WHERE show_created='y' and show_creator is NULL;

#sylvieg 2006-12-20
ALTER TABLE tiki_files ADD KEY created (created);
ALTER TABLE tiki_files ADD KEY archiveId (archiveId);
ALTER TABLE tiki_files ADD KEY galleryId (galleryId);
ALTER TABLE tiki_user_assigned_modules  DROP PRIMARY KEY , ADD PRIMARY KEY name (name(30),user,position);
ALTER TABLE tiki_theme_control_objects DROP PRIMARY KEY , ADD PRIMARY KEY objId (objId(100), type(100));
ALTER TABLE tiki_objects ADD KEY itemId (itemId, type);
ALTER TABLE users_users ADD KEY registrationDate (registrationDate);
ALTER TABLE tiki_rss_modules ADD KEY name (name);

#sylvie 2006-12-21
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_view_actionlog', 'Can view action log', 'registered', 'tiki');

# mose 2006-12-27
# this is a destructive action, but required because backups has been removed
delete from tiki_menu_options where url='tiki-backup.php';

# mose 2006-12-28
# changing username max length
ALTER TABLE tiki_newsreader_marks DROP PRIMARY KEY ;
alter table tiki_newsreader_marks change user user varchar(200) not null default '';
ALTER TABLE tiki_newsreader_marks ADD PRIMARY KEY (user(100),serverId,groupName(100));
ALTER TABLE tiki_page_footnotes DROP PRIMARY KEY ;
alter table tiki_page_footnotes change user user varchar(200) not null default '';
ALTER TABLE tiki_page_footnotes ADD PRIMARY KEY (user(150),pageName(100));
alter table tiki_user_taken_quizzes change user user varchar(200) not null default '';
ALTER TABLE tiki_user_taken_quizzes DROP PRIMARY KEY ;
ALTER TABLE tiki_user_taken_quizzes ADD PRIMARY KEY (user,quizId(50));
alter table tiki_user_votings change user user varchar(200) not null default '';
ALTER TABLE tiki_user_votings DROP PRIMARY KEY ;
ALTER TABLE tiki_user_votings ADD PRIMARY KEY (user(100),id(100));
ALTER TABLE tiki_user_watches DROP PRIMARY KEY ;
ALTER TABLE tiki_user_watches change user user varchar(200) not null default '' ;
ALTER TABLE tiki_user_watches ADD PRIMARY KEY (user(100),event,object(50));
ALTER TABLE tiki_friends DROP PRIMARY KEY ;
ALTER TABLE tiki_friends change user user varchar(200) not null default '';
ALTER TABLE tiki_friends change friend friend varchar(200) not null default '';
ALTER TABLE tiki_friends ADD PRIMARY KEY (user(120),friend(120));
ALTER TABLE tiki_friendship_requests DROP PRIMARY KEY ;
ALTER TABLE tiki_friendship_requests change userFrom userFrom varchar(200) not null default '';
ALTER TABLE tiki_friendship_requests change userTo userTo varchar(200) not null default '' ;
ALTER TABLE tiki_friendship_requests ADD PRIMARY KEY (userFrom(120),userTo(120));

alter table users_users change login login varchar(200) not null default '';
alter table tiki_wiki_attachments change user user varchar(200) not null default '';
alter table tiki_webmail_messages change user user varchar(200) not null default '';
alter table tiki_webmail_contacts change user user varchar(200) not null default '';
alter table tiki_users change user user varchar(200) not null default '';
alter table tiki_userpoints change user user varchar(200) not null default '';
alter table tiki_userfiles change user user varchar(200) not null default '';
alter table tiki_user_tasks change user user varchar(200) not null default '';
alter table tiki_user_quizzes change user user varchar(200) not null default '';
alter table tiki_user_preferences change user user varchar(200) not null default '';
alter table tiki_user_postings change user user varchar(200) not null default '';
alter table tiki_user_notes change user user varchar(200) not null default '';
alter table tiki_user_menus change user user varchar(200) not null default '';
alter table tiki_user_mail_accounts change user user varchar(200) not null default '';
alter table tiki_user_bookmarks_urls change user user varchar(200) not null default '';
alter table tiki_user_bookmarks_folders change user user varchar(200) not null default '';
alter table tiki_user_assigned_modules change user user varchar(200) not null default '';
alter table tiki_tags change user user varchar(200) not null default '';
alter table tiki_suggested_faq_questions change user user varchar(200) not null default '';
alter table tiki_submissions change author author varchar(200) not null default '';
alter table tiki_shoutbox change user user varchar(200) not null default '';
alter table tiki_sessions change user user varchar(200) not null default '';
alter table tiki_semaphores change user user varchar(200) not null default '';
alter table tiki_pages change user user varchar(200) not null default '';
alter table tiki_newsreader_servers change user user varchar(200) not null default '';
alter table tiki_minical_topics change user user varchar(200) not null default '';
alter table tiki_minical_events change user user varchar(200) not null default '';
alter table tiki_mailin_accounts change user user varchar(200) not null default '';
alter table tiki_live_support_requests change user user varchar(200) not null default '';
alter table tiki_live_support_operators change user user varchar(200) not null default '';
alter table tiki_live_support_messages change user user varchar(200) not null default '';
alter table tiki_images change user user varchar(200) not null default '';
alter table tiki_history change user user varchar(200) not null default '';
alter table tiki_galleries change user user varchar(200) not null default '';
alter table tiki_forums_reported change user user varchar(200) not null default '';
alter table tiki_forums_queue change user user varchar(200) not null default '';
alter table tiki_forum_reads change user user varchar(200) not null default '';
alter table tiki_files change user user varchar(200) not null default '';
alter table tiki_files change lockedby lockedby varchar(200) not null default '';
alter table tiki_file_galleries change user user varchar(200) not null default '';
alter table tiki_drawings change user user varchar(200) not null default '';
alter table tiki_copyrights change userName userName varchar(200) not null default '';
ALTER TABLE tiki_comments DROP KEY parentId;
ALTER TABLE tiki_comments DROP KEY parentId_2;
ALTER TABLE tiki_comments DROP KEY parentId_3;
ALTER TABLE tiki_comments DROP KEY parentId_4;
ALTER TABLE tiki_comments DROP KEY parentId_5;
ALTER TABLE tiki_comments DROP KEY parentId_6;
ALTER TABLE tiki_comments DROP KEY parentId_8;
ALTER TABLE tiki_comments DROP KEY no_repeats;
ALTER TABLE tiki_comments ADD UNIQUE KEY no_repeats(parentId, userName(40), title(100), commentDate, message_id(40), in_reply_to(40));
alter table tiki_comments change userName userName varchar(200) not null default '';
alter table tiki_charts_votes change user user varchar(200) not null default '';
alter table tiki_calendars change user user varchar(200) not null default '';
alter table tiki_calendar_roles change userName userName varchar(200) not null default '';
alter table tiki_calendar_items change user user varchar(200) not null default '';
alter table tiki_blogs change user user varchar(200) not null default '';
alter table tiki_banning change user user varchar(200) not null default '';
alter table tiki_actionlog change user user varchar(200) not null default '';
alter table messu_messages change user user varchar(200) not null default '';
alter table galaxia_workitems change user user varchar(200) not null default '';
alter table galaxia_user_roles change user user varchar(200) not null default '';
alter table galaxia_instance_comments change user user varchar(200) not null default '';
alter table galaxia_instance_activities change user user varchar(200) not null default '';
alter table tiki_freetagged_objects change user user varchar(200) not null default '';
 
#01/07/1007 sylvieg
alter table tiki_actionlog change user user varchar(200) default '';
alter table galaxia_instance_activities change user user varchar(200) default '';
alter table galaxia_instance_comments change user user varchar(200) default '';
alter table galaxia_workitems change user user varchar(200) default '';
alter table tiki_banning change user user varchar(200) default '';
alter table tiki_blog_posts change user user varchar(200) default '';
alter table tiki_blogs change user user varchar(200) default '';
alter table tiki_calendar_items change user user varchar(200) default '';
alter table tiki_comments change userName userName varchar(200) default '';
alter table tiki_copyrights change userName userName varchar(200) default '';
alter table tiki_drawings change user user varchar(200) default '';
alter table tiki_file_galleries change user user varchar(200) default '';
alter table tiki_files change user user varchar(200) default '';
alter table tiki_files change lockedby lockedby varchar(200) default '';
alter table tiki_forums_queue change user user varchar(200) default '';
alter table tiki_forums_reported change user user varchar(200) default '';
alter table tiki_galleries change user user varchar(200) default '';
alter table tiki_pages change user user varchar(200) default '';
alter table tiki_page_drafts drop primary key ;
alter table tiki_page_drafts change user user varchar(200) default '';
alter table tiki_page_drafts add primary key (pageName(120),user(120));
alter table tiki_sheet_values change user user varchar(200) default '';
alter table tiki_tracker_item_attachments change user user varchar(200) default NULL;
alter table tiki_tracker_item_comments change user user varchar(200) default NULL;
alter table tiki_user_quizzes change user user varchar(200) default '';

#01/12/2007 sylvieg
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_assign_perm_blog', 'Can assign perms to blog', 'admin', 'blogs');

#01/16/2007 sylvieg
ALTER TABLE tiki_directory_sites ADD KEY isValid (isValid);
ALTER TABLE tiki_directory_sites ADD KEY url (url);

#02/01/2007 mkalbere & Jyhem 2007-06-14
ALTER TABLE `tiki_tracker_item_fields` DROP INDEX value;
ALTER TABLE `tiki_tracker_item_fields` DROP INDEX value_1;
ALTER TABLE `tiki_tracker_item_fields` DROP INDEX value_2;
ALTER TABLE `tiki_tracker_item_fields` DROP INDEX value_3;
ALTER TABLE `tiki_tracker_item_fields` DROP INDEX value_4;
ALTER TABLE `tiki_tracker_item_fields` DROP INDEX value_5;
ALTER TABLE `tiki_tracker_item_fields` ADD FULLTEXT KEY ft (value);

#05/02/2007 niclone
CREATE TABLE IF NOT EXISTS `tiki_webmail_contacts_ext` (
  `contactId` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `value` varchar(255) NOT NULL,
  `hidden` tinyint(1) NOT NULL,
  KEY `contactId` (`contactId`)
) ENGINE=MyISAM;
CREATE TABLE IF NOT EXISTS `tiki_webmail_contacts_fields` (
  `user` VARCHAR( 200 ) NOT NULL ,
  `fieldname` VARCHAR( 255 ) NOT NULL ,
  INDEX ( `user` )
) ENGINE = MyISAM ;

#nyloth 2007-02-12
ALTER TABLE `tiki_webmail_contacts_fields` ADD `order` int(2) NOT NULL default '0';
ALTER TABLE `tiki_webmail_contacts_fields` ADD `show` char(1) NOT NULL default 'n';
ALTER TABLE `tiki_webmail_contacts_fields` ADD `fieldId` int(10) unsigned NOT NULL auto_increment PRIMARY KEY;
ALTER TABLE `tiki_webmail_contacts_ext` ADD `fieldId` int(10) unsigned NOT NULL;
UPDATE `tiki_webmail_contacts_ext` SET `fieldId` = (SELECT `fieldId` FROM `tiki_webmail_contacts_fields` WHERE `fieldname` = `name` LIMIT 1);
ALTER TABLE `tiki_webmail_contacts_ext` DROP COLUMN `name`;

#xavi/sylvie 2007-02-21
ALTER TABLE tiki_searchindex ADD KEY location(location(50), page(200));

#sylvieg 03/09/07
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_tell_a_friend', 'Can send a link to a friend', 'Basic', 'tiki');

#sylvieg 03/22/07
ALTER TABLE users_permissions ADD admin varchar(1) default NULL;
UPDATE users_permissions set admin='y' where permName = 'tiki_p_admin';
UPDATE users_permissions set admin='y' where permName = 'tiki_p_admin_categories';
UPDATE users_permissions set admin='y' where permName = 'tiki_p_admin_calendar';
UPDATE users_permissions set type='category' where permName= 'tiki_p_admin_categories';
UPDATE users_permissions set type='category' where permName= 'tiki_p_view_categories'; 
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_edit_categories', 'Can edit items in categories', 'registered', 'category');
UPDATE users_permissions set admin='y' where permName = 'tiki_p_admin_charts';
UPDATE users_permissions set admin='y' where permName = 'tiki_p_admin_chat';
UPDATE users_permissions set admin='y' where permName = 'tiki_p_admin_cms';
UPDATE users_permissions set admin='y' where permName = 'tiki_p_admin_contribution';
UPDATE users_permissions set admin='y' where permName = 'tiki_p_admin_directory';
UPDATE users_permissions set admin='y' where permName = 'tiki_p_admin_faqs';
UPDATE users_permissions set admin='y' where permName = 'tiki_p_admin_file_galleries';
UPDATE users_permissions set admin='y' where permName = 'tiki_p_admin_forum';
UPDATE users_permissions set admin='y' where permName = 'tiki_p_admin_galleries';
UPDATE users_permissions set admin='y' where permName = 'tiki_p_admin_games';
UPDATE users_permissions set admin='y' where permName = 'tiki_p_admin_newsletters';
UPDATE users_permissions set admin='y' where permName = 'tiki_p_admin_quizzes';
UPDATE users_permissions set admin='y' where permName = 'tiki_p_admin_sheet';
UPDATE users_permissions set admin='y' where permName = 'tiki_p_admin_shoutbox';
UPDATE users_permissions set admin='y' where permName = 'tiki_p_admin_surveys';
UPDATE users_permissions set admin='y' where permName = 'tiki_p_admin_trackers';
UPDATE users_permissions set admin='y' where permName = 'tiki_p_admin_users';
UPDATE users_permissions set admin='y' where permName = 'tiki_p_admin_wiki';
UPDATE users_permissions set admin='y' where permName = 'tiki_p_admin_workflow';
UPDATE users_permissions set admin='y' where permName = 'tiki_p_blog_admin';
UPDATE users_permissions set admin=NULL where permName = 'tiki_p_admin_users';
UPDATE users_permissions set type='cms' where type = 'topics';

ALTER TABLE `tiki_links` ADD INDEX `toPage` (`toPage`);

#sylvieg 2007/04/04
ALTER TABLE `tiki_actionlog_conf` ADD `id` int(11) NOT NULL auto_increment FIRST, ADD KEY id (`id`);

#sylvieg 2007/05/09
UPDATE users_permissions set permName='tiki_p_batch_upload_file_dir' where permName='tiki_p_batch_upload_files_dir';

#sylvieg 2007/05/15
ALTER TABLE tiki_forums  CHANGE name name varchar(255);
ALTER TABLE tiki_comments CHANGE title title varchar(255);

#sylvieg 2007/05/23
ALTER TABLE users_users ADD email_due int(14) default NULL AFTER pass_due;
ALTER TABLE users_users ADD unsuccessful_logins int(14) default 0;

#sylvieg 2007/05/25
ALTER TABLE users_users CHANGE email_due email_confirm int(14) default NULL;
ALTER TABLE users_users CHANGE pass_due pass_confirm int(14) default NULL;

#pkdille 2007/05/31 & Jyhem 2007-06-14
DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='r' and name='Admin' and url='tiki-admin.php' and position='1050' and section='' and perm='tiki_p_admin_users' and groupname='' ;
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_admin_users','');

#sylvieg 2007/06/01
ALTER TABLE `tiki_modules` DROP PRIMARY KEY;
ALTER TABLE `tiki_modules` ADD PRIMARY KEY  (name, position, ord);

#sylvieg 2007/06/04
ALTER TABLE `tiki_file_galleries` ADD `subgal_conf` varchar(200) default NULL;

#nkoth 2007/06/05 
#in 1.10 can be used for users without tiki_p_edit_structures
#to show and browse the list of structures. Access to the structure
#creating and edit functions are still restricted to tiki_p_edit_structures

UPDATE tiki_menu_options SET perm='tiki_p_view' where url='tiki-admin_structures.php';

#mose 07 06 07 
alter table tiki_menu_options add userlevel int(4) default 0 after groupname;

#Jyhem 2007-06-19 (obsolete perm name)
DELETE FROM `users_permissions` WHERE permName='tiki_p_batch_upload_files_dir';

#mose because tomtom is just a lazy guy 01 07 07 
ALTER TABLE `tiki_newsletters` ADD `author` varchar(200) default NULL;

#2007-07-02 sylvieg
ALTER TABLE tiki_pages CHANGE `cache` `cache` longtext;

#Jyhem 2007-07-05 (correct misleading title)
UPDATE tiki_menu_options SET name='New article' where url='tiki-edit_article.php';

#nyloth 2007-07-08
ALTER TABLE `tiki_forums` ADD `threadStyle` varchar(100) default NULL;
ALTER TABLE `tiki_forums` ADD `commentsPerPage` varchar(100) default NULL;

#pkdille 2007-07-09 (delete false quicktags)
DELETE FROM tiki_quicktags WHERE taglabel='table' AND taginsert='||r1c1|r1c2||r2c1|r2c2||' AND tagicon='images/insert_table.gif' AND tagcategory='trackers';
DELETE FROM tiki_quicktags WHERE taglabel='table' AND taginsert='||r1c1|r1c2||r2c1|r2c2||' AND tagicon='images/insert_table.gif' AND tagcategory='blogs';
DELETE FROM tiki_quicktags WHERE taglabel='table' AND taginsert='||r1c1|r1c2||r2c1|r2c2||' AND tagicon='images/insert_table.gif' AND tagcategory='calendar';
DELETE FROM tiki_quicktags WHERE taglabel='table' AND taginsert='||r1c1|r1c2||r2c1|r2c2||' AND tagicon='images/insert_table.gif' AND tagcategory='articles';
DELETE FROM tiki_quicktags WHERE taglabel='table' AND taginsert='||r1c1|r1c2||r2c1|r2c2||' AND tagicon='images/insert_table.gif' AND tagcategory='faqs';
DELETE FROM tiki_quicktags WHERE taglabel='table' AND taginsert='||r1c1|r1c2||r2c1|r2c2||' AND tagicon='images/insert_table.gif' AND tagcategory='forums';
DELETE FROM tiki_quicktags WHERE taglabel='table' AND taginsert='||r1c1|r1c2||r2c1|r2c2||' AND tagicon='images/insert_table.gif' AND tagcategory='wiki';

##2007-07-10 sylvieg - thx tibi - duplication from 1_8to1_9
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

#nyloth 2007-07-12
ALTER TABLE `tiki_forums` ADD `is_flat` char(1) default NULL;
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_forum_edit_own_posts', 'Can edit own forum posts', 'registered', 'forums');

#sylvieg 2007-07-12
ALTER TABLE tiki_actionlog_params ADD INDEX nameValue (name, value(200));

#nyloth 2007-07-15
ALTER TABLE tiki_menu_options ADD UNIQUE KEY uniq_menu(menuId,name,url,position,section,perm);
DELETE FROM tiki_menu_options where menuId='42' and type='o' and name='Search' and url='tiki-searchindex.php' and position='13' and section='feature_search' and perm='' and groupname='';
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Search','tiki-searchindex.php',13,'feature_search','','');
UPDATE tiki_menu_options SET section = 'feature_mytiki' WHERE menuId=42 AND url='tiki-my_tiki.php';
UPDATE tiki_menu_options SET section = 'feature_mytiki,feature_userPreferences' WHERE menuId=42 AND url='tiki-user_preferences.php';
UPDATE tiki_menu_options SET section = 'feature_mytiki,feature_messages' WHERE menuId=42 AND url='messu-mailbox.php';
UPDATE tiki_menu_options SET section = 'feature_mytiki,feature_tasks' WHERE menuId=42 AND url='tiki-user_tasks.php';
UPDATE tiki_menu_options SET section = 'feature_mytiki,feature_user_bookmarks' WHERE menuId=42 AND url='tiki-user_bookmarks.php';
UPDATE tiki_menu_options SET section = 'feature_mytiki,user_assigned_modules' WHERE menuId=42 AND url='tiki-user_assigned_modules.php';
UPDATE tiki_menu_options SET section = 'feature_mytiki,feature_newsreader' WHERE menuId=42 AND url='tiki-newsreader_servers.php';
UPDATE tiki_menu_options SET section = 'feature_mytiki,feature_webmail' WHERE menuId=42 AND url='tiki-webmail.php';
UPDATE tiki_menu_options SET section = 'feature_mytiki,feature_contacts' WHERE menuId=42 AND url='tiki-contacts.php';
UPDATE tiki_menu_options SET section = 'feature_mytiki,feature_notepad' WHERE menuId=42 AND url='tiki-notepad_list.php';
UPDATE tiki_menu_options SET section = 'feature_mytiki,feature_userfiles' WHERE menuId=42 AND url='tiki-userfiles.php';
UPDATE tiki_menu_options SET section = 'feature_mytiki,feature_usermenu' WHERE menuId=42 AND url='tiki-usermenu.php';
UPDATE tiki_menu_options SET section = 'feature_mytiki,feature_minical' WHERE menuId=42 AND url='tiki-minical.php';
UPDATE tiki_menu_options SET section = 'feature_mytiki,feature_user_watches' WHERE menuId=42 AND url='tiki-user_watches.php';

#sylvieg 2007-07-16
ALTER TABLE tiki_modules ADD moduleId int(8) NOT NULL auto_increment FIRST, ADD  KEY moduleId (moduleId);

#pkdille 2007-07-25
#These quicktags where wrong in tiki 1.9.8. They have to be deleted
DELETE FROM tiki_quicktags WHERE taglabel='New wms Metadata' AND taginsert='METADATA\r\n		\"wms_name\" \"myname\"\r\n		\"wms_srs\" \"EPSG:4326\"\r\n	\"wms_server_version\" \" \"\r\n	\"wms_layers\" \"mylayers\"\r\n	\"wms_request\" \"myrequest\"\r\n	\"wms_format\" \" \"\r\n	\"wms_time\" \" \"\r\n END' AND  tagicon='img/icons/admin_metatags.png' AND tagcategory='maps';
DELETE FROM tiki_quicktags WHERE taglabel='New Class' AND taginsert='CLASS\r\n EXPRESSION ()\r\n SYMBOL 0\r\n OUTLINECOLOR\r\n COLOR\r\n  NAME \"myclass\"\r\nEND #end of class' AND tagicon='img/icons/mini_triangle.gif' AND tagcategory='maps';
DELETE FROM tiki_quicktags WHERE taglabel='New Query' AND taginsert='#\r\n#Start of query definitions\r\n QUERYMAP\r\n STATUS ON\r\n STYLE HILITE\r\nEND' AND tagicon='img/icons/question.gif' AND tagcategory='maps';
DELETE FROM tiki_quicktags WHERE taglabel='New Scalebar' AND taginsert='#\r\n#start of scalebar\r\nSCALEBAR\r\n IMAGECOLOR 255 255 255\r\n STYLE 1\r\n SIZE 400 2\r\n COLOR 0 0 0\r\n  UNITS KILOMETERS\r\n INTERVALS 5\r\n STATUS ON\r\nEND' AND tagicon='img/icons/desc_lenght.gif' AND tagcategory='maps';
DELETE FROM tiki_quicktags WHERE taglabel='New Layer' AND taginsert='LAYER\r\n NAME \"mylayer\"\r\n TYPE\r\n STATUS ON\r\n DATA \"mydata\"\r\nEND #end of layer' AND tagicon='img/ed_copy.gif' AND tagcategory='maps';
DELETE FROM tiki_quicktags WHERE taglabel='New Label' AND taginsert='LABEL\r\n  COLOR\r\n ANGLE\r\n FONT arial\r\n TYPE TRUETYPE\r\n  POSITION\r\n  PARTIALS TRUE\r\n  SIZE 6\r\n  BUFFER 0\r\n OUTLINECOLOR\r\nEND #end of label' AND tagicon='img/icons/fontfamily.gif' AND tagcategory='maps';
DELETE FROM tiki_quicktags WHERE taglabel='New Reference' AND taginsert='#\r\n#start of reference\r\nREFERENCE\r\n SIZE 120 60\r\n STATUS ON\r\n  EXTENT -180 -90 182 88\r\n OUTLINECOLOR 255 0 0\r\n IMAGE \"myimagedata\"\r\nCOLOR -1 -1 -1\r\nEND' AND tagicon='images/ed_image.gif' AND tagcategory='maps';
DELETE FROM tiki_quicktags WHERE taglabel='New Legend' AND taginsert='#\r\n#start of legend\r\n#\r\nLEGENDr\n KEYSIZE 18 12\r\n POSTLABELCACHE TRUE\r\n STATUS ON\r\nEND' AND tagicon='images/ed_about.gif' AND tagcategory='maps';
DELETE FROM tiki_quicktags WHERE taglabel='New Web' AND taginsert='#\r\n#Start of web interface definition\r\n#\r\nWEB\r\n TEMPLATE \"myfile/url\"\r\n MINSCALE 1000\r\n MAXSCALE 40000\r\n IMAGEPATH \"myimagepath\"\r\n IMAGEURL \"mypath\"\r\nEND' AND tagicon='img/icons/ico_link.gif' AND tagcategory='maps';
DELETE FROM tiki_quicktags WHERE taglabel='New Mapfile' AND taginsert='#\r\n#Start of mapfile\r\n#\r\nNAME MYMAPFILE\r\n STATUS ON\r\nSIZE \r\nEXTENT\r\n UNITS\r\nSHAPEPATH \" \"\r\nIMAGETYPE \" \"\r\nFONTSET \" \"\r\nIMAGECOLOR -1 -1 -1\r\n\r\n#remove this text and add objects here\r\n\r\nEND # end of mapfile' AND tagicon='img/icons/global.gif' AND tagcategory='maps';

#pkdille 2007-07-18 and 2007-07-25
# articles
DELETE FROM tiki_quicktags WHERE taglabel='bold' AND taginsert='__text__' AND tagicon='images/ed_format_bold.gif' AND tagcategory='articles';
DELETE FROM tiki_quicktags WHERE taglabel='bold' AND taginsert='__text__' AND tagicon='pics/icons/text_bold.png' AND tagcategory='articles';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('bold','__text__','pics/icons/text_bold.png','articles');

DELETE FROM tiki_quicktags WHERE taglabel='italic' AND taginsert='\'\'text\'\'' AND tagicon='images/ed_format_italic.gif' AND tagcategory='articles';
DELETE FROM tiki_quicktags WHERE taglabel='italic' AND taginsert='\'\'text\'\'' AND tagicon='pics/icons/text_italic.png' AND tagcategory='articles';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('italic','\'\'text\'\'','pics/icons/text_italic.png','articles');

DELETE FROM tiki_quicktags WHERE taglabel='underline' AND taginsert='===text===' AND tagicon='images/ed_format_underline.gif' AND tagcategory='articles';
DELETE FROM tiki_quicktags WHERE taglabel='underline' AND taginsert='===text===' AND tagicon='pics/icons/text_underline.png' AND tagcategory='articles';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('underline','===text===','pics/icons/text_underline.png','articles');

DELETE FROM tiki_quicktags WHERE taglabel='table new' AND taginsert='||r1c1|r1c2\nr2c1|r2c2||' AND tagicon='images/insert_table.gif' AND tagcategory='articles';
DELETE FROM tiki_quicktags WHERE taglabel='table new' AND taginsert='||r1c1|r1c2\nr2c1|r2c2||' AND tagicon='pics/icons/table.png' AND tagcategory='articles';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('table new','||r1c1|r1c2\nr2c1|r2c2||','pics/icons/table.png','articles');

DELETE FROM tiki_quicktags WHERE taglabel='external link' AND taginsert='[http://example.com|text]' AND tagcategory='articles';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('external link','[http://example.com|text]','pics/icons/world_link.png','articles');

DELETE FROM tiki_quicktags WHERE taglabel='wiki link' AND taginsert='((text))' AND tagicon='images/ed_copy.gif' AND tagcategory='articles';
DELETE FROM tiki_quicktags WHERE taglabel='wiki link' AND taginsert='((text))' AND tagicon='pics/icons/page_link.png' AND tagcategory='articles';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('wiki link','((text))','pics/icons/page_link.png','articles');

DELETE FROM tiki_quicktags WHERE taglabel='title bar' AND taginsert='-=text=-' AND tagicon='images/fullscreen_maximize.gif' AND tagcategory='articles';
DELETE FROM tiki_quicktags WHERE taglabel='title bar' AND taginsert='-=text=-' AND tagicon='pics/icons/text_padding_top.png' AND tagcategory='articles';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('title bar','-=text=-','pics/icons/text_padding_top.png','articles');

DELETE FROM tiki_quicktags WHERE taglabel='heading1' AND taginsert='!text' AND tagicon='images/ed_custom.gif' AND tagcategory='articles';
DELETE FROM tiki_quicktags WHERE taglabel='heading1' AND taginsert='!text' AND tagicon='pics/icons/text_heading_1.png' AND tagcategory='articles';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('heading1','!text','pics/icons/text_heading_1.png','articles');

DELETE FROM tiki_quicktags WHERE taglabel='heading2' AND taginsert='!!text' AND tagicon='pics/icons/text_heading_2.png' AND tagcategory='articles';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('heading2','!!text','pics/icons/text_heading_2.png','articles');

DELETE FROM tiki_quicktags WHERE taglabel='heading3' AND taginsert='!!!text' AND tagicon='pics/icons/text_heading_3.png' AND tagcategory='articles';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('heading3','!!!text','pics/icons/text_heading_3.png','articles');

DELETE FROM tiki_quicktags WHERE taglabel='box' AND taginsert='^text^' AND tagicon='images/ed_about.gif' AND tagcategory='articles';
DELETE FROM tiki_quicktags WHERE taglabel='box' AND taginsert='^text^' AND tagicon='pics/icons/box.png' AND tagcategory='articles';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('box','^text^','pics/icons/box.png','articles');

DELETE FROM tiki_quicktags WHERE taglabel='rss feed' AND taginsert='{rss id= }' AND tagicon='images/ico_link.gif' AND tagcategory='articles';
DELETE FROM tiki_quicktags WHERE taglabel='rss feed' AND taginsert='{rss id= }' AND tagicon='pics/icons/feed.png' AND tagcategory='articles';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('rss feed','{rss id= }','pics/icons/feed.png','articles');

DELETE FROM tiki_quicktags WHERE taglabel='dynamic content' AND taginsert='{content id= }' AND tagcategory='articles';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('dynamic content','{content id= }','pics/icons/database_refresh.png','articles');

DELETE FROM tiki_quicktags WHERE taglabel='center text' AND taginsert='::text::' AND tagicon='images/ed_align_center.gif' AND tagcategory='articles';
DELETE FROM tiki_quicktags WHERE taglabel='center text' AND taginsert='::text::' AND tagicon='pics/icons/text_align_center.png' AND tagcategory='articles';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('center text','::text::','pics/icons/text_align_center.png','articles');

DELETE FROM tiki_quicktags WHERE taglabel='colored text' AND taginsert='~~#FF0000:text~~' AND tagicon='images/fontfamily.gif' AND tagcategory='articles';
DELETE FROM tiki_quicktags WHERE taglabel='colored text' AND taginsert='~~#FF0000:text~~' AND tagicon='pics/icons/palette.png' AND tagcategory='articles';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('colored text','~~#FF0000:text~~','pics/icons/palette.png','articles');

DELETE FROM tiki_quicktags WHERE taglabel='tagline' AND taginsert='{cookie}' AND tagicon='images/footprint.gif' AND tagcategory='articles';
DELETE FROM tiki_quicktags WHERE taglabel='tagline' AND taginsert='{cookie}' AND tagicon='pics/icons/database_go.png' AND tagcategory='articles';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('tagline','{cookie}','pics/icons/database_go.png','articles');

DELETE FROM tiki_quicktags WHERE taglabel='dynamic variable' AND taginsert='%text%' AND tagicon='images/book.gif' AND tagcategory='articles';
DELETE FROM tiki_quicktags WHERE taglabel='dynamic variable' AND taginsert='%text%' AND tagicon='pics/icons/book_open.png' AND tagcategory='articles';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('dynamic variable','%text%','pics/icons/book_open.png','articles');

DELETE FROM tiki_quicktags WHERE taglabel='hr' AND taginsert='---' AND tagicon='images/ed_hr.gif' AND tagcategory='articles';
DELETE FROM tiki_quicktags WHERE taglabel='hr' AND taginsert='---' AND tagicon='pics/icons/page.png' AND tagcategory='articles';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('hr','---','pics/icons/page.png','articles');

DELETE FROM tiki_quicktags WHERE taglabel='image' AND taginsert='{img src= width= height= align= desc= link= }' AND tagicon='images/ed_image.gif' AND tagcategory='articles';
DELETE FROM tiki_quicktags WHERE taglabel='image' AND taginsert='{img src= width= height= align= desc= link= }' AND tagicon='pics/icons/picture.png' AND tagcategory='articles';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('image','{img src= width= height= align= desc= link= }','pics/icons/picture.png','articles');

# blogs
DELETE FROM tiki_quicktags WHERE taglabel='bold' AND taginsert='__text__' AND tagicon='images/ed_format_bold.gif' AND tagcategory='blogs';
DELETE FROM tiki_quicktags WHERE taglabel='bold' AND taginsert='__text__' AND tagicon='pics/icons/text_bold.png' AND tagcategory='blogs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('bold','__text__','pics/icons/text_bold.png','blogs');

DELETE FROM tiki_quicktags WHERE taglabel='italic' AND taginsert='\'\'text\'\'' AND tagicon='images/ed_format_italic.gif' AND tagcategory='blogs';
DELETE FROM tiki_quicktags WHERE taglabel='italic' AND taginsert='\'\'text\'\'' AND tagicon='pics/icons/text_italic.png' AND tagcategory='blogs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('italic','\'\'text\'\'','pics/icons/text_italic.png','blogs');

DELETE FROM tiki_quicktags WHERE taglabel='underline' AND taginsert='===text===' AND tagicon='images/ed_format_underline.gif' AND tagcategory='blogs';
DELETE FROM tiki_quicktags WHERE taglabel='underline' AND taginsert='===text===' AND tagicon='pics/icons/text_underline.png' AND tagcategory='blogs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('underline','===text===','pics/icons/text_underline.png','blogs');

DELETE FROM tiki_quicktags WHERE taglabel='table new' AND taginsert='||r1c1|r1c2\nr2c1|r2c2||' AND tagicon='images/insert_table.gif' AND tagcategory='blogs';
DELETE FROM tiki_quicktags WHERE taglabel='table new' AND taginsert='||r1c1|r1c2\nr2c1|r2c2||' AND tagicon='pics/icons/table.png' AND tagcategory='blogs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('table new','||r1c1|r1c2\nr2c1|r2c2||','pics/icons/table.png','blogs');

DELETE FROM tiki_quicktags WHERE taglabel='external link' AND taginsert='[http://example.com|text]' AND tagcategory='blogs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('external link','[http://example.com|text]','pics/icons/world_link.png','blogs');

DELETE FROM tiki_quicktags WHERE taglabel='wiki link' AND taginsert='((text))' AND tagicon='images/ed_copy.gif' AND tagcategory='blogs';
DELETE FROM tiki_quicktags WHERE taglabel='wiki link' AND taginsert='((text))' AND tagicon='pics/icons/page_link.png' AND tagcategory='blogs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('wiki link','((text))','pics/icons/page_link.png','blogs');

DELETE FROM tiki_quicktags WHERE taglabel='title bar' AND taginsert='-=text=-' AND tagicon='images/fullscreen_maximize.gif' AND tagcategory='blogs';
DELETE FROM tiki_quicktags WHERE taglabel='title bar' AND taginsert='-=text=-' AND tagicon='pics/icons/text_padding_top.png' AND tagcategory='blogs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('title bar','-=text=-','pics/icons/text_padding_top.png','blogs');

DELETE FROM tiki_quicktags WHERE taglabel='heading1' AND taginsert='!text' AND tagicon='images/ed_custom.gif' AND tagcategory='blogs';
DELETE FROM tiki_quicktags WHERE taglabel='heading1' AND taginsert='!text' AND tagicon='pics/icons/text_heading_1.png' AND tagcategory='blogs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('heading1','!text','pics/icons/text_heading_1.png','blogs');

DELETE FROM tiki_quicktags WHERE taglabel='heading2' AND taginsert='!!text' AND tagicon='pics/icons/text_heading_2.png' AND tagcategory='blogs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('heading2','!!text','pics/icons/text_heading_2.png','blogs');

DELETE FROM tiki_quicktags WHERE taglabel='heading3' AND taginsert='!!!text' AND tagicon='pics/icons/text_heading_3.png' AND tagcategory='blogs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('heading3','!!!text','pics/icons/text_heading_3.png','blogs');

DELETE FROM tiki_quicktags WHERE taglabel='box' AND taginsert='^text^' AND tagicon='images/ed_about.gif' AND tagcategory='blogs';
DELETE FROM tiki_quicktags WHERE taglabel='box' AND taginsert='^text^' AND tagicon='pics/icons/box.png' AND tagcategory='blogs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('box','^text^','pics/icons/box.png','blogs');

DELETE FROM tiki_quicktags WHERE taglabel='rss feed' AND taginsert='{rss id= }' AND tagicon='images/ico_link.gif' AND tagcategory='blogs';
DELETE FROM tiki_quicktags WHERE taglabel='rss feed' AND taginsert='{rss id= }' AND tagicon='pics/icons/feed.png' AND tagcategory='blogs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('rss feed','{rss id= }','pics/icons/feed.png','blogs');

DELETE FROM tiki_quicktags WHERE taglabel='dynamic content' AND taginsert='{content id= }' AND tagcategory='blogs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('dynamic content','{content id= }','pics/icons/database_refresh.png','blogs');

DELETE FROM tiki_quicktags WHERE taglabel='center text' AND taginsert='::text::' AND tagicon='images/ed_align_center.gif' AND tagcategory='blogs';
DELETE FROM tiki_quicktags WHERE taglabel='center text' AND taginsert='::text::' AND tagicon='pics/icons/text_align_center.png' AND tagcategory='blogs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('center text','::text::','pics/icons/text_align_center.png','blogs');

DELETE FROM tiki_quicktags WHERE taglabel='colored text' AND taginsert='~~#FF0000:text~~' AND tagicon='images/fontfamily.gif' AND tagcategory='blogs';
DELETE FROM tiki_quicktags WHERE taglabel='colored text' AND taginsert='~~#FF0000:text~~' AND tagicon='pics/icons/palette.png' AND tagcategory='blogs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('colored text','~~#FF0000:text~~','pics/icons/palette.png','blogs');

DELETE FROM tiki_quicktags WHERE taglabel='tagline' AND taginsert='{cookie}' AND tagicon='images/footprint.gif' AND tagcategory='blogs';
DELETE FROM tiki_quicktags WHERE taglabel='tagline' AND taginsert='{cookie}' AND tagicon='pics/icons/database_go.png' AND tagcategory='blogs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('tagline','{cookie}','pics/icons/database_go.png','blogs');

DELETE FROM tiki_quicktags WHERE taglabel='dynamic variable' AND taginsert='%text%' AND tagicon='images/book.gif' AND tagcategory='blogs';
DELETE FROM tiki_quicktags WHERE taglabel='dynamic variable' AND taginsert='%text%' AND tagicon='pics/icons/book_open.png' AND tagcategory='blogs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('dynamic variable','%text%','pics/icons/book_open.png','blogs');

DELETE FROM tiki_quicktags WHERE taglabel='hr' AND taginsert='---' AND tagicon='images/ed_hr.gif' AND tagcategory='blogs';
DELETE FROM tiki_quicktags WHERE taglabel='hr' AND taginsert='---' AND tagicon='pics/icons/page.png' AND tagcategory='blogs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('hr','---','pics/icons/page.png','blogs');

DELETE FROM tiki_quicktags WHERE taglabel='image' AND taginsert='{img src= width= height= align= desc= link= }' AND tagicon='images/ed_image.gif' AND tagcategory='blogs';
DELETE FROM tiki_quicktags WHERE taglabel='image' AND taginsert='{img src= width= height= align= desc= link= }' AND tagicon='pics/icons/picture.png' AND tagcategory='blogs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('image','{img src= width= height= align= desc= link= }','pics/icons/picture.png','blogs');

#calendar
DELETE FROM tiki_quicktags WHERE taglabel='bold' AND taginsert='__text__' AND tagicon='images/ed_format_bold.gif' AND tagcategory='calendar';
DELETE FROM tiki_quicktags WHERE taglabel='bold' AND taginsert='__text__' AND tagicon='pics/icons/text_bold.png' AND tagcategory='calendar';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('bold','__text__','pics/icons/text_bold.png','calendar');

DELETE FROM tiki_quicktags WHERE taglabel='italic' AND taginsert='\'\'text\'\'' AND tagicon='images/ed_format_italic.gif' AND tagcategory='calendar';
DELETE FROM tiki_quicktags WHERE taglabel='italic' AND taginsert='\'\'text\'\'' AND tagicon='pics/icons/text_italic.png' AND tagcategory='calendar';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('italic','\'\'text\'\'','pics/icons/text_italic.png','calendar');

DELETE FROM tiki_quicktags WHERE taglabel='underline' AND taginsert='===text===' AND tagicon='images/ed_format_underline.gif' AND tagcategory='calendar';
DELETE FROM tiki_quicktags WHERE taglabel='underline' AND taginsert='===text===' AND tagicon='pics/icons/text_underline.png' AND tagcategory='calendar';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('underline','===text===','pics/icons/text_underline.png','calendar');

DELETE FROM tiki_quicktags WHERE taglabel='table new' AND taginsert='||r1c1|r1c2\nr2c1|r2c2||' AND tagicon='images/insert_table.gif' AND tagcategory='calendar';
DELETE FROM tiki_quicktags WHERE taglabel='table new' AND taginsert='||r1c1|r1c2\nr2c1|r2c2||' AND tagicon='pics/icons/table.png' AND tagcategory='calendar';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('table new','||r1c1|r1c2\nr2c1|r2c2||','pics/icons/table.png','calendar');

DELETE FROM tiki_quicktags WHERE taglabel='external link' AND taginsert='[http://example.com|text]' AND tagcategory='calendar';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('external link','[http://example.com|text]','pics/icons/world_link.png','calendar');

DELETE FROM tiki_quicktags WHERE taglabel='wiki link' AND taginsert='((text))' AND tagicon='images/ed_copy.gif' AND tagcategory='calendar';
DELETE FROM tiki_quicktags WHERE taglabel='wiki link' AND taginsert='((text))' AND tagicon='pics/icons/page_link.png' AND tagcategory='calendar';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('wiki link','((text))','pics/icons/page_link.png','calendar');

DELETE FROM tiki_quicktags WHERE taglabel='title bar' AND taginsert='-=text=-' AND tagicon='images/fullscreen_maximize.gif' AND tagcategory='calendar';
DELETE FROM tiki_quicktags WHERE taglabel='title bar' AND taginsert='-=text=-' AND tagicon='pics/icons/text_padding_top.png' AND tagcategory='calendar';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('title bar','-=text=-','pics/icons/text_padding_top.png','calendar');

DELETE FROM tiki_quicktags WHERE taglabel='heading1' AND taginsert='!text' AND tagicon='images/ed_custom.gif' AND tagcategory='calendar';
DELETE FROM tiki_quicktags WHERE taglabel='heading1' AND taginsert='!text' AND tagicon='pics/icons/text_heading_1.png' AND tagcategory='calendar';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('heading1','!text','pics/icons/text_heading_1.png','calendar');

DELETE FROM tiki_quicktags WHERE taglabel='heading2' AND taginsert='!!text' AND tagicon='pics/icons/text_heading_2.png' AND tagcategory='calendar';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('heading2','!!text','pics/icons/text_heading_2.png','calendar');

DELETE FROM tiki_quicktags WHERE taglabel='heading3' AND taginsert='!!!text' AND tagicon='pics/icons/text_heading_3.png' AND tagcategory='calendar';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('heading3','!!!text','pics/icons/text_heading_3.png','calendar');

DELETE FROM tiki_quicktags WHERE taglabel='box' AND taginsert='^text^' AND tagicon='images/ed_about.gif' AND tagcategory='calendar';
DELETE FROM tiki_quicktags WHERE taglabel='box' AND taginsert='^text^' AND tagicon='pics/icons/box.png' AND tagcategory='calendar';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('box','^text^','pics/icons/box.png','calendar');

DELETE FROM tiki_quicktags WHERE taglabel='rss feed' AND taginsert='{rss id= }' AND tagicon='images/ico_link.gif' AND tagcategory='calendar';
DELETE FROM tiki_quicktags WHERE taglabel='rss feed' AND taginsert='{rss id= }' AND tagicon='pics/icons/feed.png' AND tagcategory='calendar';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('rss feed','{rss id= }','pics/icons/feed.png','calendar');

DELETE FROM tiki_quicktags WHERE taglabel='dynamic content' AND taginsert='{content id= }' AND tagcategory='calendar';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('dynamic content','{content id= }','pics/icons/database_refresh.png','calendar');

DELETE FROM tiki_quicktags WHERE taglabel='center text' AND taginsert='::text::' AND tagicon='images/ed_align_center.gif' AND tagcategory='calendar';
DELETE FROM tiki_quicktags WHERE taglabel='center text' AND taginsert='::text::' AND tagicon='pics/icons/text_align_center.png' AND tagcategory='calendar';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('center text','::text::','pics/icons/text_align_center.png','calendar');

DELETE FROM tiki_quicktags WHERE taglabel='colored text' AND taginsert='~~#FF0000:text~~' AND tagicon='images/fontfamily.gif' AND tagcategory='calendar';
DELETE FROM tiki_quicktags WHERE taglabel='colored text' AND taginsert='~~#FF0000:text~~' AND tagicon='pics/icons/palette.png' AND tagcategory='calendar';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('colored text','~~#FF0000:text~~','pics/icons/palette.png','calendar');

DELETE FROM tiki_quicktags WHERE taglabel='tagline' AND taginsert='{cookie}' AND tagicon='images/footprint.gif' AND tagcategory='calendar';
DELETE FROM tiki_quicktags WHERE taglabel='tagline' AND taginsert='{cookie}' AND tagicon='pics/icons/database_go.png' AND tagcategory='calendar';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('tagline','{cookie}','pics/icons/database_go.png','calendar');

DELETE FROM tiki_quicktags WHERE taglabel='dynamic variable' AND taginsert='%text%' AND tagicon='images/book.gif' AND tagcategory='calendar';
DELETE FROM tiki_quicktags WHERE taglabel='dynamic variable' AND taginsert='%text%' AND tagicon='pics/icons/book_open.png' AND tagcategory='calendar';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('dynamic variable','%text%','pics/icons/book_open.png','calendar');

DELETE FROM tiki_quicktags WHERE taglabel='hr' AND taginsert='---' AND tagicon='images/ed_hr.gif' AND tagcategory='calendar';
DELETE FROM tiki_quicktags WHERE taglabel='hr' AND taginsert='---' AND tagicon='pics/icons/page.png' AND tagcategory='calendar';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('hr','---','pics/icons/page.png','calendar');

DELETE FROM tiki_quicktags WHERE taglabel='image' AND taginsert='{img src= width= height= align= desc= link= }' AND tagicon='images/ed_image.gif' AND tagcategory='calendar';
DELETE FROM tiki_quicktags WHERE taglabel='image' AND taginsert='{img src= width= height= align= desc= link= }' AND tagicon='pics/icons/picture.png' AND tagcategory='calendar';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('image','{img src= width= height= align= desc= link= }','pics/icons/picture.png','calendar');

# faqs
DELETE FROM tiki_quicktags WHERE taglabel='bold' AND taginsert='__text__' AND tagicon='images/ed_format_bold.gif' AND tagcategory='faqs';
DELETE FROM tiki_quicktags WHERE taglabel='bold' AND taginsert='__text__' AND tagicon='pics/icons/text_bold.png' AND tagcategory='faqs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('bold','__text__','pics/icons/text_bold.png','faqs');

DELETE FROM tiki_quicktags WHERE taglabel='italic' AND taginsert='\'\'text\'\'' AND tagicon='images/ed_format_italic.gif' AND tagcategory='faqs';
DELETE FROM tiki_quicktags WHERE taglabel='italic' AND taginsert='\'\'text\'\'' AND tagicon='pics/icons/text_italic.png' AND tagcategory='faqs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('italic','\'\'text\'\'','pics/icons/text_italic.png','faqs');

DELETE FROM tiki_quicktags WHERE taglabel='underline' AND taginsert='===text===' AND tagicon='images/ed_format_underline.gif' AND tagcategory='faqs';
DELETE FROM tiki_quicktags WHERE taglabel='underline' AND taginsert='===text===' AND tagicon='pics/icons/text_underline.png' AND tagcategory='faqs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('underline','===text===','pics/icons/text_underline.png','faqs');

DELETE FROM tiki_quicktags WHERE taglabel='table new' AND taginsert='||r1c1|r1c2\nr2c1|r2c2||' AND tagicon='images/insert_table.gif' AND tagcategory='faqs';
DELETE FROM tiki_quicktags WHERE taglabel='table new' AND taginsert='||r1c1|r1c2\nr2c1|r2c2||' AND tagicon='pics/icons/table.png' AND tagcategory='faqs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('table new','||r1c1|r1c2\nr2c1|r2c2||','pics/icons/table.png','faqs');

DELETE FROM tiki_quicktags WHERE taglabel='external link' AND taginsert='[http://example.com|text]' AND tagcategory='faqs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('external link','[http://example.com|text]','pics/icons/world_link.png','faqs');

DELETE FROM tiki_quicktags WHERE taglabel='wiki link' AND taginsert='((text))' AND tagicon='images/ed_copy.gif' AND tagcategory='faqs';
DELETE FROM tiki_quicktags WHERE taglabel='wiki link' AND taginsert='((text))' AND tagicon='pics/icons/page_link.png' AND tagcategory='faqs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('wiki link','((text))','pics/icons/page_link.png','faqs');

DELETE FROM tiki_quicktags WHERE taglabel='title bar' AND taginsert='-=text=-' AND tagicon='images/fullscreen_maximize.gif' AND tagcategory='faqs';
DELETE FROM tiki_quicktags WHERE taglabel='title bar' AND taginsert='-=text=-' AND tagicon='pics/icons/text_padding_top.png' AND tagcategory='faqs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('title bar','-=text=-','pics/icons/text_padding_top.png','faqs');

DELETE FROM tiki_quicktags WHERE taglabel='heading1' AND taginsert='!text' AND tagicon='images/ed_custom.gif' AND tagcategory='faqs';
DELETE FROM tiki_quicktags WHERE taglabel='heading1' AND taginsert='!text' AND tagicon='pics/icons/text_heading_1.png' AND tagcategory='faqs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('heading1','!text','pics/icons/text_heading_1.png','faqs');

DELETE FROM tiki_quicktags WHERE taglabel='heading2' AND taginsert='!!text' AND tagicon='pics/icons/text_heading_2.png' AND tagcategory='faqs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('heading2','!!text','pics/icons/text_heading_2.png','faqs');

DELETE FROM tiki_quicktags WHERE taglabel='heading3' AND taginsert='!!!text' AND tagicon='pics/icons/text_heading_3.png' AND tagcategory='faqs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('heading3','!!!text','pics/icons/text_heading_3.png','faqs');

DELETE FROM tiki_quicktags WHERE taglabel='box' AND taginsert='^text^' AND tagicon='images/ed_about.gif' AND tagcategory='faqs';
DELETE FROM tiki_quicktags WHERE taglabel='box' AND taginsert='^text^' AND tagicon='pics/icons/box.png' AND tagcategory='faqs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('box','^text^','pics/icons/box.png','faqs');

DELETE FROM tiki_quicktags WHERE taglabel='rss feed' AND taginsert='{rss id= }' AND tagicon='images/ico_link.gif' AND tagcategory='faqs';
DELETE FROM tiki_quicktags WHERE taglabel='rss feed' AND taginsert='{rss id= }' AND tagicon='pics/icons/feed.png' AND tagcategory='faqs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('rss feed','{rss id= }','pics/icons/feed.png','faqs');

DELETE FROM tiki_quicktags WHERE taglabel='dynamic content' AND taginsert='{content id= }' AND tagcategory='faqs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('dynamic content','{content id= }','pics/icons/database_refresh.png','faqs');

DELETE FROM tiki_quicktags WHERE taglabel='center text' AND taginsert='::text::' AND tagicon='images/ed_align_center.gif' AND tagcategory='faqs';
DELETE FROM tiki_quicktags WHERE taglabel='center text' AND taginsert='::text::' AND tagicon='pics/icons/text_align_center.png' AND tagcategory='faqs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('center text','::text::','pics/icons/text_align_center.png','faqs');

DELETE FROM tiki_quicktags WHERE taglabel='colored text' AND taginsert='~~#FF0000:text~~' AND tagicon='images/fontfamily.gif' AND tagcategory='faqs';
DELETE FROM tiki_quicktags WHERE taglabel='colored text' AND taginsert='~~#FF0000:text~~' AND tagicon='pics/icons/palette.png' AND tagcategory='faqs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('colored text','~~#FF0000:text~~','pics/icons/palette.png','faqs');

DELETE FROM tiki_quicktags WHERE taglabel='tagline' AND taginsert='{cookie}' AND tagicon='images/footprint.gif' AND tagcategory='faqs';
DELETE FROM tiki_quicktags WHERE taglabel='tagline' AND taginsert='{cookie}' AND tagicon='pics/icons/database_go.png' AND tagcategory='faqs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('tagline','{cookie}','pics/icons/database_go.png','faqs');

DELETE FROM tiki_quicktags WHERE taglabel='dynamic variable' AND taginsert='%text%' AND tagicon='images/book.gif' AND tagcategory='faqs';
DELETE FROM tiki_quicktags WHERE taglabel='dynamic variable' AND taginsert='%text%' AND tagicon='pics/icons/book_open.png' AND tagcategory='faqs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('dynamic variable','%text%','pics/icons/book_open.png','faqs');

DELETE FROM tiki_quicktags WHERE taglabel='hr' AND taginsert='---' AND tagicon='images/ed_hr.gif' AND tagcategory='faqs';
DELETE FROM tiki_quicktags WHERE taglabel='hr' AND taginsert='---' AND tagicon='pics/icons/page.png' AND tagcategory='faqs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('hr','---','pics/icons/page.png','faqs');

DELETE FROM tiki_quicktags WHERE taglabel='image' AND taginsert='{img src= width= height= align= desc= link= }' AND tagicon='images/ed_image.gif' AND tagcategory='faqs';
DELETE FROM tiki_quicktags WHERE taglabel='image' AND taginsert='{img src= width= height= align= desc= link= }' AND tagicon='pics/icons/picture.png' AND tagcategory='faqs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('image','{img src= width= height= align= desc= link= }','pics/icons/picture.png','faqs');

# forums
DELETE FROM tiki_quicktags WHERE taglabel='bold' AND taginsert='__text__' AND tagicon='images/ed_format_bold.gif' AND tagcategory='forums';
DELETE FROM tiki_quicktags WHERE taglabel='bold' AND taginsert='__text__' AND tagicon='pics/icons/text_bold.png' AND tagcategory='forums';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('bold','__text__','pics/icons/text_bold.png','forums');

DELETE FROM tiki_quicktags WHERE taglabel='italic' AND taginsert='\'\'text\'\'' AND tagicon='images/ed_format_italic.gif' AND tagcategory='forums';
DELETE FROM tiki_quicktags WHERE taglabel='italic' AND taginsert='\'\'text\'\'' AND tagicon='pics/icons/text_italic.png' AND tagcategory='forums';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('italic','\'\'text\'\'','pics/icons/text_italic.png','forums');

DELETE FROM tiki_quicktags WHERE taglabel='underline' AND taginsert='===text===' AND tagicon='images/ed_format_underline.gif' AND tagcategory='forums';
DELETE FROM tiki_quicktags WHERE taglabel='underline' AND taginsert='===text===' AND tagicon='pics/icons/text_underline.png' AND tagcategory='forums';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('underline','===text===','pics/icons/text_underline.png','forums');

DELETE FROM tiki_quicktags WHERE taglabel='table new' AND taginsert='||r1c1|r1c2\nr2c1|r2c2||' AND tagicon='images/insert_table.gif' AND tagcategory='forums';
DELETE FROM tiki_quicktags WHERE taglabel='table new' AND taginsert='||r1c1|r1c2\nr2c1|r2c2||' AND tagicon='pics/icons/table.png' AND tagcategory='forums';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('table new','||r1c1|r1c2\nr2c1|r2c2||','pics/icons/table.png','forums');

DELETE FROM tiki_quicktags WHERE taglabel='external link' AND taginsert='[http://example.com|text]' AND tagcategory='forums';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('external link','[http://example.com|text]','pics/icons/world_link.png','forums');

DELETE FROM tiki_quicktags WHERE taglabel='wiki link' AND taginsert='((text))' AND tagicon='images/ed_copy.gif' AND tagcategory='forums';
DELETE FROM tiki_quicktags WHERE taglabel='wiki link' AND taginsert='((text))' AND tagicon='pics/icons/page_link.png' AND tagcategory='forums';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('wiki link','((text))','pics/icons/page_link.png','forums');

DELETE FROM tiki_quicktags WHERE taglabel='title bar' AND taginsert='-=text=-' AND tagicon='images/fullscreen_maximize.gif' AND tagcategory='forums';
DELETE FROM tiki_quicktags WHERE taglabel='title bar' AND taginsert='-=text=-' AND tagicon='pics/icons/text_padding_top.png' AND tagcategory='forums';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('title bar','-=text=-','pics/icons/text_padding_top.png','forums');

DELETE FROM tiki_quicktags WHERE taglabel='heading1' AND taginsert='!text' AND tagicon='images/ed_custom.gif' AND tagcategory='forums';
DELETE FROM tiki_quicktags WHERE taglabel='heading1' AND taginsert='!text' AND tagicon='pics/icons/text_heading_1.png' AND tagcategory='forums';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('heading1','!text','pics/icons/text_heading_1.png','forums');

DELETE FROM tiki_quicktags WHERE taglabel='heading2' AND taginsert='!!text' AND tagicon='pics/icons/text_heading_2.png' AND tagcategory='forums';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('heading2','!!text','pics/icons/text_heading_2.png','forums');

DELETE FROM tiki_quicktags WHERE taglabel='heading3' AND taginsert='!!!text' AND tagicon='pics/icons/text_heading_3.png' AND tagcategory='forums';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('heading3','!!!text','pics/icons/text_heading_3.png','forums');

DELETE FROM tiki_quicktags WHERE taglabel='box' AND taginsert='^text^' AND tagicon='images/ed_about.gif' AND tagcategory='forums';
DELETE FROM tiki_quicktags WHERE taglabel='box' AND taginsert='^text^' AND tagicon='pics/icons/box.png' AND tagcategory='forums';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('box','^text^','pics/icons/box.png','forums');

DELETE FROM tiki_quicktags WHERE taglabel='rss feed' AND taginsert='{rss id= }' AND tagicon='images/ico_link.gif' AND tagcategory='forums';
DELETE FROM tiki_quicktags WHERE taglabel='rss feed' AND taginsert='{rss id= }' AND tagicon='pics/icons/feed.png' AND tagcategory='forums';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('rss feed','{rss id= }','pics/icons/feed.png','forums');

DELETE FROM tiki_quicktags WHERE taglabel='dynamic content' AND taginsert='{content id= }' AND tagcategory='forums';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('dynamic content','{content id= }','pics/icons/database_refresh.png','forums');

DELETE FROM tiki_quicktags WHERE taglabel='center text' AND taginsert='::text::' AND tagicon='images/ed_align_center.gif' AND tagcategory='forums';
DELETE FROM tiki_quicktags WHERE taglabel='center text' AND taginsert='::text::' AND tagicon='pics/icons/text_align_center.png' AND tagcategory='forums';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('center text','::text::','pics/icons/text_align_center.png','forums');

DELETE FROM tiki_quicktags WHERE taglabel='colored text' AND taginsert='~~#FF0000:text~~' AND tagicon='images/fontfamily.gif' AND tagcategory='forums';
DELETE FROM tiki_quicktags WHERE taglabel='colored text' AND taginsert='~~#FF0000:text~~' AND tagicon='pics/icons/palette.png' AND tagcategory='forums';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('colored text','~~#FF0000:text~~','pics/icons/palette.png','forums');

DELETE FROM tiki_quicktags WHERE taglabel='tagline' AND taginsert='{cookie}' AND tagicon='images/footprint.gif' AND tagcategory='forums';
DELETE FROM tiki_quicktags WHERE taglabel='tagline' AND taginsert='{cookie}' AND tagicon='pics/icons/database_go.png' AND tagcategory='forums';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('tagline','{cookie}','pics/icons/database_go.png','forums');

DELETE FROM tiki_quicktags WHERE taglabel='dynamic variable' AND taginsert='%text%' AND tagicon='images/book.gif' AND tagcategory='forums';
DELETE FROM tiki_quicktags WHERE taglabel='dynamic variable' AND taginsert='%text%' AND tagicon='pics/icons/book_open.png' AND tagcategory='forums';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('dynamic variable','%text%','pics/icons/book_open.png','forums');

DELETE FROM tiki_quicktags WHERE taglabel='hr' AND taginsert='---' AND tagicon='images/ed_hr.gif' AND tagcategory='forums';
DELETE FROM tiki_quicktags WHERE taglabel='hr' AND taginsert='---' AND tagicon='pics/icons/page.png' AND tagcategory='forums';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('hr','---','pics/icons/page.png','forums');

DELETE FROM tiki_quicktags WHERE taglabel='image' AND taginsert='{img src= width= height= align= desc= link= }' AND tagicon='images/ed_image.gif' AND tagcategory='forums';
DELETE FROM tiki_quicktags WHERE taglabel='image' AND taginsert='{img src= width= height= align= desc= link= }' AND tagicon='pics/icons/picture.png' AND tagcategory='forums';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('image','{img src= width= height= align= desc= link= }','pics/icons/picture.png','forums');

#trackers
DELETE FROM tiki_quicktags WHERE taglabel='bold' AND taginsert='__text__' AND tagicon='images/ed_format_bold.gif' AND tagcategory='trackers';
DELETE FROM tiki_quicktags WHERE taglabel='bold' AND taginsert='__text__' AND tagicon='pics/icons/text_bold.png' AND tagcategory='trackers';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('bold','__text__','pics/icons/text_bold.png','trackers');

DELETE FROM tiki_quicktags WHERE taglabel='italic' AND taginsert='\'\'text\'\'' AND tagicon='images/ed_format_italic.gif' AND tagcategory='trackers';
DELETE FROM tiki_quicktags WHERE taglabel='italic' AND taginsert='\'\'text\'\'' AND tagicon='pics/icons/text_italic.png' AND tagcategory='trackers';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('italic','\'\'text\'\'','pics/icons/text_italic.png','trackers');

DELETE FROM tiki_quicktags WHERE taglabel='underline' AND taginsert='===text===' AND tagicon='images/ed_format_underline.gif' AND tagcategory='trackers';
DELETE FROM tiki_quicktags WHERE taglabel='underline' AND taginsert='===text===' AND tagicon='pics/icons/text_underline.png' AND tagcategory='trackers';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('underline','===text===','pics/icons/text_underline.png','trackers');

DELETE FROM tiki_quicktags WHERE taglabel='table new' AND taginsert='||r1c1|r1c2\nr2c1|r2c2||' AND tagicon='images/insert_table.gif' AND tagcategory='trackers';
DELETE FROM tiki_quicktags WHERE taglabel='table new' AND taginsert='||r1c1|r1c2\nr2c1|r2c2||' AND tagicon='pics/icons/table.png' AND tagcategory='trackers';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('table new','||r1c1|r1c2\nr2c1|r2c2||','pics/icons/table.png','trackers');

DELETE FROM tiki_quicktags WHERE taglabel='external link' AND taginsert='[http://example.com|text]' AND tagcategory='trackers';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('external link','[http://example.com|text]','pics/icons/world_link.png','trackers');

DELETE FROM tiki_quicktags WHERE taglabel='wiki link' AND taginsert='((text))' AND tagicon='images/ed_copy.gif' AND tagcategory='trackers';
DELETE FROM tiki_quicktags WHERE taglabel='wiki link' AND taginsert='((text))' AND tagicon='pics/icons/page_link.png' AND tagcategory='trackers';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('wiki link','((text))','pics/icons/page_link.png','trackers');

DELETE FROM tiki_quicktags WHERE taglabel='title bar' AND taginsert='-=text=-' AND tagicon='images/fullscreen_maximize.gif' AND tagcategory='trackers';
DELETE FROM tiki_quicktags WHERE taglabel='title bar' AND taginsert='-=text=-' AND tagicon='pics/icons/text_padding_top.png' AND tagcategory='trackers';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('title bar','-=text=-','pics/icons/text_padding_top.png','trackers');

DELETE FROM tiki_quicktags WHERE taglabel='heading1' AND taginsert='!text' AND tagicon='images/ed_custom.gif' AND tagcategory='trackers';
DELETE FROM tiki_quicktags WHERE taglabel='heading1' AND taginsert='!text' AND tagicon='pics/icons/text_heading_1.png' AND tagcategory='trackers';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('heading1','!text','pics/icons/text_heading_1.png','trackers');

DELETE FROM tiki_quicktags WHERE taglabel='heading2' AND taginsert='!!text' AND tagicon='pics/icons/text_heading_2.png' AND tagcategory='trackers';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('heading2','!!text','pics/icons/text_heading_2.png','trackers');

DELETE FROM tiki_quicktags WHERE taglabel='heading3' AND taginsert='!!!text' AND tagicon='pics/icons/text_heading_3.png' AND tagcategory='trackers';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('heading3','!!!text','pics/icons/text_heading_3.png','trackers');

DELETE FROM tiki_quicktags WHERE taglabel='box' AND taginsert='^text^' AND tagicon='images/ed_about.gif' AND tagcategory='trackers';
DELETE FROM tiki_quicktags WHERE taglabel='box' AND taginsert='^text^' AND tagicon='pics/icons/box.png' AND tagcategory='trackers';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('box','^text^','pics/icons/box.png','trackers');

DELETE FROM tiki_quicktags WHERE taglabel='rss feed' AND taginsert='{rss id= }' AND tagicon='images/ico_link.gif' AND tagcategory='trackers';
DELETE FROM tiki_quicktags WHERE taglabel='rss feed' AND taginsert='{rss id= }' AND tagicon='pics/icons/feed.png' AND tagcategory='trackers';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('rss feed','{rss id= }','pics/icons/feed.png','trackers');

DELETE FROM tiki_quicktags WHERE taglabel='dynamic content' AND taginsert='{content id= }' AND tagcategory='trackers';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('dynamic content','{content id= }','pics/icons/database_refresh.png','trackers');

DELETE FROM tiki_quicktags WHERE taglabel='center text' AND taginsert='::text::' AND tagicon='images/ed_align_center.gif' AND tagcategory='trackers';
DELETE FROM tiki_quicktags WHERE taglabel='center text' AND taginsert='::text::' AND tagicon='pics/icons/text_align_center.png' AND tagcategory='trackers';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('center text','::text::','pics/icons/text_align_center.png','trackers');

DELETE FROM tiki_quicktags WHERE taglabel='colored text' AND taginsert='~~#FF0000:text~~' AND tagicon='images/fontfamily.gif' AND tagcategory='trackers';
DELETE FROM tiki_quicktags WHERE taglabel='colored text' AND taginsert='~~#FF0000:text~~' AND tagicon='pics/icons/palette.png' AND tagcategory='trackers';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('colored text','~~#FF0000:text~~','pics/icons/palette.png','trackers');

DELETE FROM tiki_quicktags WHERE taglabel='tagline' AND taginsert='{cookie}' AND tagicon='images/footprint.gif' AND tagcategory='trackers';
DELETE FROM tiki_quicktags WHERE taglabel='tagline' AND taginsert='{cookie}' AND tagicon='pics/icons/database_go.png' AND tagcategory='trackers';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('tagline','{cookie}','pics/icons/database_go.png','trackers');

DELETE FROM tiki_quicktags WHERE taglabel='dynamic variable' AND taginsert='%text%' AND tagicon='images/book.gif' AND tagcategory='trackers';
DELETE FROM tiki_quicktags WHERE taglabel='dynamic variable' AND taginsert='%text%' AND tagicon='pics/icons/book_open.png' AND tagcategory='trackers';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('dynamic variable','%text%','pics/icons/book_open.png','trackers');

DELETE FROM tiki_quicktags WHERE taglabel='hr' AND taginsert='---' AND tagicon='images/ed_hr.gif' AND tagcategory='trackers';
DELETE FROM tiki_quicktags WHERE taglabel='hr' AND taginsert='---' AND tagicon='pics/icons/page.png' AND tagcategory='trackers';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('hr','---','pics/icons/page.png','trackers');

DELETE FROM tiki_quicktags WHERE taglabel='image' AND taginsert='{img src= width= height= align= desc= link= }' AND tagicon='images/ed_image.gif' AND tagcategory='trackers';
DELETE FROM tiki_quicktags WHERE taglabel='image' AND taginsert='{img src= width= height= align= desc= link= }' AND tagicon='pics/icons/picture.png' AND tagcategory='trackers';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('image','{img src= width= height= align= desc= link= }','pics/icons/picture.png','trackers');

# maps
DELETE FROM tiki_quicktags WHERE taglabel='New wms Metadata' AND taginsert='METADATA\r\n		\"wms_name\" \"myname\"\r\n 	"wms_srs" "EPSG:4326"\r\n 	"wms_server_version" " "\r\n 	"wms_layers" "mylayers"\r\n 	"wms_request" "myrequest"\r\n 	"wms_format" " "\r\n 	"wms_time" " "\r\n END' AND tagicon='img/icons/admin_metatags.png' AND tagcategory='maps';
DELETE FROM tiki_quicktags WHERE taglabel='New wms Metadata' AND taginsert='METADATA\r\n		\"wms_name\" \"myname\"\r\n 	"wms_srs" "EPSG:4326"\r\n 	"wms_server_version" " "\r\n 	"wms_layers" "mylayers"\r\n 	"wms_request" "myrequest"\r\n 	"wms_format" " "\r\n 	"wms_time" " "\r\n END' AND tagicon='pics/icons/tag_blue_add.png' AND tagcategory='maps';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('New wms Metadata','METADATA\r\n		\"wms_name\" \"myname\"\r\n 	"wms_srs" "EPSG:4326"\r\n 	"wms_server_version" " "\r\n 	"wms_layers" "mylayers"\r\n 	"wms_request" "myrequest"\r\n 	"wms_format" " "\r\n 	"wms_time" " "\r\n END', 'pics/icons/tag_blue_add.png','maps');

DELETE FROM tiki_quicktags WHERE taglabel='New Class' AND taginsert='CLASS\r\n EXPRESSION ()\r\n SYMBOL 0\r\n OUTLINECOLOR\r\n COLOR\r\n NAME "myclass" \r\nEND #end of class' AND tagicon='img/icons/mini_triangle.gif' AND tagcategory='maps';
DELETE FROM tiki_quicktags WHERE taglabel='New Class' AND taginsert='CLASS\r\n EXPRESSION ()\r\n SYMBOL 0\r\n OUTLINECOLOR\r\n COLOR\r\n NAME "myclass" \r\nEND #end of class' AND tagicon='pics/icons/application_add.png' AND tagcategory='maps';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('New Class', 'CLASS\r\n EXPRESSION ()\r\n SYMBOL 0\r\n OUTLINECOLOR\r\n COLOR\r\n NAME "myclass" \r\nEND #end of class', 'pics/icons/application_add.png','maps');

DELETE FROM tiki_quicktags WHERE taglabel='New Projection' AND taginsert='PROJECTION\r\n "init=epsg:4326"\r\nEND' AND tagicon='images/ico_mode.gif' AND tagcategory='maps';
DELETE FROM tiki_quicktags WHERE taglabel='New Projection' AND taginsert='PROJECTION\r\n "init=epsg:4326"\r\nEND' AND tagicon='pics/icons/image_add.png' AND tagcategory='maps';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('New Projection','PROJECTION\r\n "init=epsg:4326"\r\nEND','pics/icons/image_add.png','maps');

DELETE FROM tiki_quicktags WHERE taglabel='New Query' AND taginsert='#\r\n# Start of query definitions\r\n#\r\n QUERYMAP\r\n STATUS ON\r\n STYLE HILITE\r\nEND' AND tagicon='img/icons/questions.gif' AND tagcategory='maps';
DELETE FROM tiki_quicktags WHERE taglabel='New Query' AND taginsert='#\r\n# Start of query definitions\r\n#\r\n QUERYMAP\r\n STATUS ON\r\n STYLE HILITE\r\nEND' AND tagicon='pics/icons/database_gear.png' AND tagcategory='maps';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('New Query','#\r\n# Start of query definitions\r\n#\r\n QUERYMAP\r\n STATUS ON\r\n STYLE HILITE\r\nEND','pics/icons/database_gear.png','maps');

DELETE FROM tiki_quicktags WHERE taglabel='New Scalebar' AND taginsert='#\r\n# Start of scalebar\r\n#\r\nSCALEBAR\r\n IMAGECOLOR 255 255 255\r\n STYLE 1\r\n SIZE 400 2\r\n COLOR 0 0 0\r\n UNITS KILOMETERS\r\n INTERVALS 5\r\n STATUS ON\r\nEND' AND tagicon='img/icons/desc_length.gif' AND tagcategory='maps';
DELETE FROM tiki_quicktags WHERE taglabel='New Scalebar' AND taginsert='#\r\n# Start of scalebar\r\n#\r\nSCALEBAR\r\n IMAGECOLOR 255 255 255\r\n STYLE 1\r\n SIZE 400 2\r\n COLOR 0 0 0\r\n UNITS KILOMETERS\r\n INTERVALS 5\r\n STATUS ON\r\nEND' AND tagicon='pics/icons/layout_add.png' AND tagcategory='maps';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('New Scalebar','#\r\n# Start of scalebar\r\n#\r\nSCALEBAR\r\n IMAGECOLOR 255 255 255\r\n STYLE 1\r\n SIZE 400 2\r\n COLOR 0 0 0\r\n UNITS KILOMETERS\r\n INTERVALS 5\r\n STATUS ON\r\nEND','pics/icons/layout_add.png','maps');

DELETE FROM tiki_quicktags WHERE taglabel='New Layer' AND taginsert='LAYER\r\n NAME\r\n TYPE\r\n STATUS ON\r\n DATA "mydata"\r\nEND #end of layer' AND tagicon='images/ed_copy.gif' AND tagcategory='maps';
DELETE FROM tiki_quicktags WHERE taglabel='New Layer' AND taginsert='LAYER\r\n NAME\r\n TYPE\r\n STATUS ON\r\n DATA "mydata"\r\nEND #end of layer' AND tagicon='pics/icons/layers.png' AND tagcategory='maps';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('New Layer','LAYER\r\n NAME\r\n TYPE\r\n STATUS ON\r\n DATA "mydata"\r\nEND #end of layer', 'pics/icons/layers.png', 'maps');

DELETE FROM tiki_quicktags WHERE taglabel='New Label' AND taginsert='LABEL\r\n COLOR\r\n ANGLE\r\n FONT arial\r\n TYPE TRUETYPE\r\n POSITION\r\n PARTIALS TRUE\r\n SIZE 6\r\n BUFFER 0\r\n OUTLINECOLOR \r\nEND #end of label' AND tagicon='img/icons/fontfamily.gif' AND tagcategory='maps';
DELETE FROM tiki_quicktags WHERE taglabel='New Label' AND taginsert='LABEL\r\n COLOR\r\n ANGLE\r\n FONT arial\r\n TYPE TRUETYPE\r\n POSITION\r\n PARTIALS TRUE\r\n SIZE 6\r\n BUFFER 0\r\n OUTLINECOLOR \r\nEND #end of label' AND tagicon='pics/icons/comment_add.png' AND tagcategory='maps';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('New Label','LABEL\r\n COLOR\r\n ANGLE\r\n FONT arial\r\n TYPE TRUETYPE\r\n POSITION\r\n PARTIALS TRUE\r\n SIZE 6\r\n BUFFER 0\r\n OUTLINECOLOR \r\nEND #end of label', 'pics/icons/comment_add.png', 'maps');

DELETE FROM tiki_quicktags WHERE taglabel='New Reference' AND taginsert='#\r\n#start of reference\r\n#\r\n REFERENCE\r\n SIZE 120 60\r\n STATUS ON\r\n EXTENT -180 -90 182 88\r\n OUTLINECOLOR 255 0 0\r\n IMAGE "myimagedata"\r\n COLOR -1 -1 -1\r\nEND' AND tagicon='images/ed_image.gif' AND tagcategory='maps';
DELETE FROM tiki_quicktags WHERE taglabel='New Reference' AND taginsert='#\r\n#start of reference\r\n#\r\n REFERENCE\r\n SIZE 120 60\r\n STATUS ON\r\n EXTENT -180 -90 182 88\r\n OUTLINECOLOR 255 0 0\r\n IMAGE "myimagedata"\r\n COLOR -1 -1 -1\r\nEND' AND tagicon='pics/icons/picture_add.png' AND tagcategory='maps';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('New Reference','#\r\n#start of reference\r\n#\r\n REFERENCE\r\n SIZE 120 60\r\n STATUS ON\r\n EXTENT -180 -90 182 88\r\n OUTLINECOLOR 255 0 0\r\n IMAGE "myimagedata"\r\n COLOR -1 -1 -1\r\nEND','pics/icons/picture_add.png','maps');

DELETE FROM tiki_quicktags WHERE taglabel='New Legend' AND taginsert='#\r\n#start of Legend\r\n#\r\n LEGEND\r\n KEYSIZE 18 12\r\n POSTLABELCACHE TRUE\r\n STATUS ON\r\nEND' AND tagicon='images/ed_about.gif' AND tagcategory='maps';
DELETE FROM tiki_quicktags WHERE taglabel='New Legend' AND taginsert='#\r\n#start of Legend\r\n#\r\n LEGEND\r\n KEYSIZE 18 12\r\n POSTLABELCACHE TRUE\r\n STATUS ON\r\nEND' AND tagicon='pics/icons/note_add.png' AND tagcategory='maps';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('New Legend','#\r\n#start of Legend\r\n#\r\n LEGEND\r\n KEYSIZE 18 12\r\n POSTLABELCACHE TRUE\r\n STATUS ON\r\nEND','pics/icons/note_add.png','maps');

DELETE FROM tiki_quicktags WHERE taglabel='New Web' AND taginsert='#\r\n# Start of web interface definition\r\n#\r\nWEB\r\n TEMPLATE "myfile/url"\r\n MINSCALE 1000\r\n MAXSCALE 40000\r\n IMAGEPATH "myimagepath"\r\n IMAGEURL "mypath"\r\nEND' AND tagicon='img/icons/ico_link.gif' AND tagcategory='maps';
DELETE FROM tiki_quicktags WHERE taglabel='New Web' AND taginsert='#\r\n# Start of web interface definition\r\n#\r\nWEB\r\n TEMPLATE "myfile/url"\r\n MINSCALE 1000\r\n MAXSCALE 40000\r\n IMAGEPATH "myimagepath"\r\n IMAGEURL "mypath"\r\nEND' AND tagicon='pics/icons/world_link.png' AND tagcategory='maps';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('New Web','#\r\n# Start of web interface definition\r\n#\r\nWEB\r\n TEMPLATE "myfile/url"\r\n MINSCALE 1000\r\n MAXSCALE 40000\r\n IMAGEPATH "myimagepath"\r\n IMAGEURL "mypath"\r\nEND', 'pics/icons/world_link.png', 'maps');

DELETE FROM tiki_quicktags WHERE taglabel='New Outputformat' AND taginsert='OUTPUTFORMAT\r\n NAME\r\n DRIVER " "\r\n MIMETYPE "myimagetype"\r\n IMAGEMODE RGB\r\n EXTENSION "png"\r\nEND' AND tagicon='img/icons/opera.gif' AND tagcategory='maps';
DELETE FROM tiki_quicktags WHERE taglabel='New Outputformat' AND taginsert='OUTPUTFORMAT\r\n NAME\r\n DRIVER " "\r\n MIMETYPE "myimagetype"\r\n IMAGEMODE RGB\r\n EXTENSION "png"\r\nEND' AND tagicon='pics/icons/newspaper_go.png' AND tagcategory='maps';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('New Outputformat','OUTPUTFORMAT\r\n NAME\r\n DRIVER " "\r\n MIMETYPE "myimagetype"\r\n IMAGEMODE RGB\r\n EXTENSION "png"\r\nEND','pics/icons/newspaper_go.png','maps');

DELETE FROM tiki_quicktags WHERE taglabel='New Mapfile' AND taginsert='#\r\n# Start of mapfile\r\n#\r\nNAME MYMAPFLE\r\n STATUS ON\r\nSIZE \r\nEXTENT\r\nUNITS \r\nSHAPEPATH " "\r\nIMAGETYPE " "\r\nFONTSET " "\r\nIMAGECOLOR -1 -1 -1\r\n\r\n#remove this text and add objects here\r\n\r\nEND # end of mapfile' AND tagicon='img/icons/global.gif' AND tagcategory='maps';
DELETE FROM tiki_quicktags WHERE taglabel='New Mapfile' AND taginsert='#\r\n# Start of mapfile\r\n#\r\nNAME MYMAPFLE\r\n STATUS ON\r\nSIZE \r\nEXTENT\r\nUNITS \r\nSHAPEPATH " "\r\nIMAGETYPE " "\r\nFONTSET " "\r\nIMAGECOLOR -1 -1 -1\r\n\r\n#remove this text and add objects here\r\n\r\nEND # end of mapfile' AND tagicon='pics/icons/world_add.png' AND tagcategory='maps';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('New Mapfile','#\r\n# Start of mapfile\r\n#\r\nNAME MYMAPFLE\r\n STATUS ON\r\nSIZE \r\nEXTENT\r\nUNITS \r\nSHAPEPATH " "\r\nIMAGETYPE " "\r\nFONTSET " "\r\nIMAGECOLOR -1 -1 -1\r\n\r\n#remove this text and add objects here\r\n\r\nEND # end of mapfile','pics/icons/world_add.png','maps');

#Newsletters
DELETE FROM tiki_quicktags WHERE taglabel='bold' AND taginsert='__text__' AND tagicon='images/ed_format_bold.gif' AND tagcategory='newsletters';
DELETE FROM tiki_quicktags WHERE taglabel='bold' AND taginsert='__text__' AND tagicon='pics/icons/text_bold.png' AND tagcategory='newsletters';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('bold','__text__','pics/icons/text_bold.png','newsletters');

DELETE FROM tiki_quicktags WHERE taglabel='italic' AND taginsert='\'\'text\'\'' AND tagicon='images/ed_format_italic.gif' AND tagcategory='newsletters';
DELETE FROM tiki_quicktags WHERE taglabel='italic' AND taginsert='\'\'text\'\'' AND tagicon='pics/icons/text_italic.png' AND tagcategory='newsletters';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('italic','\'\'text\'\'','pics/icons/text_italic.png','newsletters');

DELETE FROM tiki_quicktags WHERE taglabel='underline' AND taginsert='===text===' AND tagicon='images/ed_format_underline.gif' AND tagcategory='newsletters';
DELETE FROM tiki_quicktags WHERE taglabel='underline' AND taginsert='===text===' AND tagicon='pics/icons/text_underline.png' AND tagcategory='newsletters';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('underline','===text===','pics/icons/text_underline.png','newsletters');

DELETE FROM tiki_quicktags WHERE taglabel='external link' AND tagcategory='newsletters';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('external link','[http://example.com|text|nocache]','pics/icons/world_link.png','newsletters');

DELETE FROM tiki_quicktags WHERE taglabel='heading1' AND taginsert='!text' AND tagicon='images/ed_custom.gif' AND tagcategory='newsletters';
DELETE FROM tiki_quicktags WHERE taglabel='heading1' AND taginsert='!text' AND tagicon='pics/icons/text_heading_1.png' AND tagcategory='newsletters';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('heading1','!text','pics/icons/text_heading_1.png','newsletters');

DELETE FROM tiki_quicktags WHERE taglabel='heading2' AND taginsert='!!text' AND tagicon='pics/icons/text_heading_2.png' AND tagcategory='newsletters';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('heading2','!!text','pics/icons/text_heading_2.png','newsletters');

DELETE FROM tiki_quicktags WHERE taglabel='heading3' AND taginsert='!!!text' AND tagicon='pics/icons/text_heading_3.png' AND tagcategory='newsletters';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('heading3','!!!text','pics/icons/text_heading_3.png','newsletters');

DELETE FROM tiki_quicktags WHERE taglabel='center text' AND taginsert='::text::' AND tagicon='images/ed_align_center.gif' AND tagcategory='newsletters';
DELETE FROM tiki_quicktags WHERE taglabel='center text' AND taginsert='::text::' AND tagicon='pics/icons/text_align_center.png' AND tagcategory='newsletters';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('center text','::text::','pics/icons/text_align_center.png','newsletters');

DELETE FROM tiki_quicktags WHERE taglabel='colored text' AND taginsert='~~#FF0000:text~~' AND tagicon='images/fontfamily.gif' AND tagcategory='newsletters';
DELETE FROM tiki_quicktags WHERE taglabel='colored text' AND taginsert='~~#FF0000:text~~' AND tagicon='pics/icons/palette.png' AND tagcategory='newsletters';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('colored text','~~#FF0000:text~~','pics/icons/palette.png','newsletters');

DELETE FROM tiki_quicktags WHERE taglabel='hr' AND taginsert='---' AND tagicon='images/ed_hr.gif' AND tagcategory='newsletters';
DELETE FROM tiki_quicktags WHERE taglabel='hr' AND taginsert='---' AND tagicon='pics/icons/page.png' AND tagcategory='newsletters';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('hr','---','pics/icons/page.png','newsletters');

DELETE FROM tiki_quicktags WHERE taglabel='image' AND taginsert='{img src= width= height= align= desc= link= }' AND tagicon='images/ed_image.gif' AND tagcategory='newsletters';
DELETE FROM tiki_quicktags WHERE taglabel='image' AND taginsert='{img src= width= height= align= desc= link= }' AND tagicon='pics/icons/picture.png' AND tagcategory='newsletters';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('image','{img src= width= height= align= desc= link= }','pics/icons/picture.png','newsletters');

#Wiki
DELETE FROM tiki_quicktags WHERE taglabel='bold' AND taginsert='__text__' AND tagicon='images/ed_format_bold.gif' AND tagcategory='wiki';
DELETE FROM tiki_quicktags WHERE taglabel='bold' AND taginsert='__text__' AND tagicon='pics/icons/text_bold.png' AND tagcategory='wiki';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('bold','__text__','pics/icons/text_bold.png','wiki');

DELETE FROM tiki_quicktags WHERE taglabel='italic' AND taginsert='\'\'text\'\'' AND tagicon='images/ed_format_italic.gif' AND tagcategory='wiki';
DELETE FROM tiki_quicktags WHERE taglabel='italic' AND taginsert='\'\'text\'\'' AND tagicon='pics/icons/text_italic.png' AND tagcategory='wiki';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('italic','\'\'text\'\'','pics/icons/text_italic.png','wiki');

DELETE FROM tiki_quicktags WHERE taglabel='underline' AND taginsert='===text===' AND tagicon='images/ed_format_underline.gif' AND tagcategory='wiki';
DELETE FROM tiki_quicktags WHERE taglabel='underline' AND taginsert='===text===' AND tagicon='pics/icons/text_underline.png' AND tagcategory='wiki';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('underline','===text===','pics/icons/text_underline.png','wiki');

DELETE FROM tiki_quicktags WHERE taglabel='table new' AND taginsert='||r1c1|r1c2\nr2c1|r2c2||' AND tagicon='images/insert_table.gif' AND tagcategory='wiki';
DELETE FROM tiki_quicktags WHERE taglabel='table new' AND taginsert='||r1c1|r1c2\nr2c1|r2c2||' AND tagicon='pics/icons/table.png' AND tagcategory='wiki';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('table new','||r1c1|r1c2\nr2c1|r2c2||','pics/icons/table.png','wiki');

DELETE FROM tiki_quicktags WHERE taglabel='external link' AND taginsert='[http://example.com|text]' AND tagcategory='wiki';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('external link','[http://example.com|text]','pics/icons/world_link.png','wiki');

DELETE FROM tiki_quicktags WHERE taglabel='wiki link' AND taginsert='((text))' AND tagicon='images/ed_copy.gif' AND tagcategory='wiki';
DELETE FROM tiki_quicktags WHERE taglabel='wiki link' AND taginsert='((text))' AND tagicon='pics/icons/page_link.png' AND tagcategory='wiki';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('wiki link','((text))','pics/icons/page_link.png','wiki');

DELETE FROM tiki_quicktags WHERE taglabel='title bar' AND taginsert='-=text=-' AND tagicon='images/fullscreen_maximize.gif' AND tagcategory='wiki';
DELETE FROM tiki_quicktags WHERE taglabel='title bar' AND taginsert='-=text=-' AND tagicon='pics/icons/text_padding_top.png' AND tagcategory='wiki';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('title bar','-=text=-','pics/icons/text_padding_top.png','wiki');

DELETE FROM tiki_quicktags WHERE taglabel='heading1' AND taginsert='!text' AND tagicon='images/ed_custom.gif' AND tagcategory='wiki';
DELETE FROM tiki_quicktags WHERE taglabel='heading1' AND taginsert='!text' AND tagicon='pics/icons/text_heading_1.png' AND tagcategory='wiki';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('heading1','!text','pics/icons/text_heading_1.png','wiki');

DELETE FROM tiki_quicktags WHERE taglabel='heading2' AND taginsert='!!text' AND tagicon='pics/icons/text_heading_2.png' AND tagcategory='wiki';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('heading2','!!text','pics/icons/text_heading_2.png','wiki');

DELETE FROM tiki_quicktags WHERE taglabel='heading3' AND taginsert='!!!text' AND tagicon='pics/icons/text_heading_3.png' AND tagcategory='wiki';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('heading3','!!!text','pics/icons/text_heading_3.png','wiki');

DELETE FROM tiki_quicktags WHERE taglabel='box' AND taginsert='^text^' AND tagicon='images/ed_about.gif' AND tagcategory='wiki';
DELETE FROM tiki_quicktags WHERE taglabel='box' AND taginsert='^text^' AND tagicon='pics/icons/box.png' AND tagcategory='wiki';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('box','^text^','pics/icons/box.png','wiki');

DELETE FROM tiki_quicktags WHERE taglabel='rss feed' AND taginsert='{rss id= }' AND tagicon='images/ico_link.gif' AND tagcategory='wiki';
DELETE FROM tiki_quicktags WHERE taglabel='rss feed' AND taginsert='{rss id= }' AND tagicon='pics/icons/feed.png' AND tagcategory='wiki';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('rss feed','{rss id= }','pics/icons/feed.png','wiki');

DELETE FROM tiki_quicktags WHERE taglabel='dynamic content' AND taginsert='{content id= }' AND tagcategory='wiki';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('dynamic content','{content id= }','pics/icons/database_refresh.png','wiki');

DELETE FROM tiki_quicktags WHERE taglabel='center text' AND taginsert='::text::' AND tagicon='images/ed_align_center.gif' AND tagcategory='wiki';
DELETE FROM tiki_quicktags WHERE taglabel='center text' AND taginsert='::text::' AND tagicon='pics/icons/text_align_center.png' AND tagcategory='wiki';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('center text','::text::','pics/icons/text_align_center.png','wiki');

DELETE FROM tiki_quicktags WHERE taglabel='colored text' AND taginsert='~~#FF0000:text~~' AND tagicon='images/fontfamily.gif' AND tagcategory='wiki';
DELETE FROM tiki_quicktags WHERE taglabel='colored text' AND taginsert='~~#FF0000:text~~' AND tagicon='pics/icons/palette.png' AND tagcategory='wiki';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('colored text','~~#FF0000:text~~','pics/icons/palette.png','wiki');

DELETE FROM tiki_quicktags WHERE taglabel='tagline' AND taginsert='{cookie}' AND tagicon='images/footprint.gif' AND tagcategory='wiki';
DELETE FROM tiki_quicktags WHERE taglabel='tagline' AND taginsert='{cookie}' AND tagicon='pics/icons/database_go.png' AND tagcategory='wiki';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('tagline','{cookie}','pics/icons/database_go.png','wiki');

DELETE FROM tiki_quicktags WHERE taglabel='dynamic variable' AND taginsert='%text%' AND tagicon='images/book.gif' AND tagcategory='wiki';
DELETE FROM tiki_quicktags WHERE taglabel='dynamic variable' AND taginsert='%text%' AND tagicon='pics/icons/book_open.png' AND tagcategory='wiki';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('dynamic variable','%text%','pics/icons/book_open.png','wiki');

DELETE FROM tiki_quicktags WHERE taglabel='hr' AND taginsert='---' AND tagicon='images/ed_hr.gif' AND tagcategory='wiki';
DELETE FROM tiki_quicktags WHERE taglabel='hr' AND taginsert='---' AND tagicon='pics/icons/page.png' AND tagcategory='wiki';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('hr','---','pics/icons/page.png','wiki');

DELETE FROM tiki_quicktags WHERE taglabel='image' AND taginsert='{img src= width= height= align= desc= link= }' AND tagicon='images/ed_image.gif' AND tagcategory='wiki';
DELETE FROM tiki_quicktags WHERE taglabel='image' AND taginsert='{img src= width= height= align= desc= link= }' AND tagicon='pics/icons/picture.png' AND tagcategory='wiki';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('image','{img src= width= height= align= desc= link= }','pics/icons/picture.png','wiki');

#sylvieg 2007-07-19
ALTER TABLE tiki_menu_options DROP KEY uniq_menu;
ALTER TABLE tiki_menu_options ADD UNIQUE KEY uniq_menu (menuId,name(50),url(80),position,section(50),perm(50));

#lphuberdeau 2007-07-19
INSERT IGNORE INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_share_page', 'Can use the share page interface', 'registered', 'wiki page');

#nkoth 2007-07-20
DELETE FROM users_permissions WHERE permName='tiki_p_share_page';
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_assign_perm_wiki_page', 'Can assign perms to wiki pages', 'admin', 'wiki');

#2007-07-18 sylvieg
ALTER TABLE users_users ADD valid varchar(32) default NULL;

#2007-08-01 sylvieg
ALTER TABLE tiki_webmail_contacts_ext CHANGE value value VARCHAR(255) NOT NULL;
AlTER TABLE tiki_webmail_contacts_fields CHANGE fieldname fieldname VARCHAR( 255 ) NOT NULL;

#2007-08-02 sylvieg
DELETE FROM tiki_quicktags WHERE taginsert='*text' and tagcategory='wiki'; 
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('list bullets', '*text', 'pics/icons/text_list_bullets.png', 'wiki');
DELETE FROM tiki_quicktags WHERE taginsert='#text' and tagcategory='wiki'; 
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('list numbers', '#text', 'pics/icons/text_list_numbers.png', 'wiki');
#2007-08-03 sylvieg
ALTER TABLE tiki_user_assigned_modules ADD moduleId int(8) FIRST;
UPDATE tiki_user_assigned_modules tuam set moduleId= (SELECT moduleId FROM tiki_modules tm WHERE tuam.name = tm.name  LIMIT 1);

#2007-08-06 niclone
CREATE TABLE `tiki_mypage` (
  `id` int(11) NOT NULL auto_increment,
  `id_users` int(11) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `id_users` (`id_users`)
) ENGINE=MyISAM;

CREATE TABLE `tiki_mypagewin` (
  `id` int(11) NOT NULL auto_increment,
  `id_mypage` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `inbody` enum('n','y') NOT NULL default 'n',
  `modal` enum('n','y') NOT NULL default 'n',
  `left` int(11) NOT NULL,
  `top` int(11) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `contenttype` enum('iframe','wiki') default NULL,
  `content` text,
  PRIMARY KEY  (`id`),
  KEY `id_mypage` (`id_mypage`)
) ENGINE=MyISAM;

#2007-07-06 niclone
ALTER TABLE `tiki_mypage` ADD `name` VARCHAR( 255 ) NOT NULL ,
ADD `description` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE `tiki_mypage` ADD INDEX `name` ( `name` ) ;

#2007-08-07 sylvieg
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_view_mypage', 'Can view any mypage', 'basic', 'mypage');
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_edit_own_mypage', 'Can view/edit only one\'s own mypages', 'registered', 'mypage');
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_edit_mypage', 'Can edit any mypage', 'registered', 'mypage');
INSERT INTO users_permissions (permName, permDesc, level, type, admin) VALUES ('tiki_p_admin_mypage', 'Can admin any mypage', 'admin', 'mypage','y');
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_list_mypage', 'Can list mypages', 'admin', 'mypage');
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_list_mypage_component', 'Can list components', 'basic', 'component');
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_view_component', 'Can view a component', 'basic', 'component');
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_assign_perm_mypage', 'Can assign perms to mypage', 'admin', 'mypage');
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_assign_perm_component', 'Can assign perms to component', 'admin', 'component');
DELETE FROM users_permissions where type = 'component';

#2007-08-13 nyloth
ALTER TABLE tiki_forums_queue ADD COLUMN `in_reply_to` varchar(128) default NULL;

#2007-08-14 niclone
ALTER TABLE `tiki_mypage` ADD `id_types` INT NOT NULL AFTER `id_users`;
ALTER TABLE `tiki_mypage` ADD INDEX `id_types` ( `id_types` );

#2007-08-15 niclone
CREATE TABLE `tiki_mypage_types` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) collate utf8_bin NOT NULL,
  `description` varchar(255) collate utf8_bin NOT NULL,
  `section` varchar(255) collate utf8_bin default NULL,
  `permissions` varchar(255) collate utf8_bin default NULL,
  PRIMARY KEY  (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM;

CREATE TABLE `tiki_mypage_types_components` (
  `id_mypage_types` int(11) NOT NULL,
  `compname` varchar(255) collate utf8_bin NOT NULL,
  `mincount` int(11) NOT NULL default '1',
  `maxcount` int(11) NOT NULL default '1',
  KEY `id_mypage_types` (`id_mypage_types`)
) ENGINE=MyISAM;

#2007-08-17 niclone
ALTER TABLE `tiki_mypage` ADD `bgcolor` VARCHAR( 16 ) NULL ;

#2007-08-17 niclone
ALTER TABLE `tiki_mypagewin` CHANGE `content` `config` BLOB NULL DEFAULT NULL;
ALTER TABLE `tiki_mypagewin` ADD `content` BLOB NULL DEFAULT NULL;

#2007-08-21 niclone
ALTER TABLE `tiki_mypage_types` ADD `def_height` INT NULL,
	ADD `def_width` INT NULL,
	ADD `fix_dimensions` ENUM( 'no', 'yes' ) NOT NULL,
	ADD `def_bgcolor` VARCHAR( 8 ) NULL,
	ADD `fix_bgcolor` ENUM( 'no', 'yes' ) NOT NULL;

#2007-08-25 niclone
ALTER TABLE `tiki_mypagewin` CHANGE `contenttype` `contenttype` VARCHAR(31) NULL DEFAULT NULL;

#2007-08-27 niclone
ALTER TABLE `tiki_mypage` ADD `created` INT NOT NULL AFTER `id_types`, ADD `modified` INT NOT NULL AFTER `created`, ADD `viewed` INT NOT NULL AFTER `modified`;
ALTER TABLE `tiki_mypagewin` ADD `created` INT NOT NULL AFTER `id_mypage`, ADD `modified` INT NOT NULL AFTER `created`, ADD `viewed` INT NOT NULL AFTER `modified`;
ALTER TABLE `tiki_mypage_types` ADD `created` INT NOT NULL AFTER `id`, ADD `modified` INT NOT NULL AFTER `created`;

#2007-09-02 sylvieg
ALTER TABLE  `tiki_mypage_types` CHANGE `name` `name` varchar(255) collate utf8_bin NOT NULL;

#2007-09-02 mose
ALTER TABLE `tiki_received_pages` ADD `parent` VARCHAR( 255 ) NULL , ADD `position` TINYINT UNSIGNED NULL , ADD `alias` VARCHAR( 255 ) NULL ;

#2007-09-06 mose problem of collision beetween 'page' in mod-menupage and mod-tracker
update `tiki_modules` set params=replace(params,'page=','pagemenu=') where name like 'menupage%';

#2007-09-08 openid support
ALTER TABLE `users_users` ADD `openid_url` VARCHAR( 255 ) NULL ;
ALTER TABLE `users_users` ADD INDEX ( `openid_url` ) ;

#2007-09-10 niclone
alter table tiki_mypage_types add column `templateuser` int not null;

#2007-09-12 niclone
alter table tiki_mypage add column `winbgcolor` varchar(16) null;
alter table tiki_mypage add column `wintitlecolor` varchar(16) null;
alter table tiki_mypage add column `wintextcolor` varchar(16) null;

#2007-09-14 niclone
alter table tiki_mypage add column `bgimage` varchar(255) NULL;
alter table tiki_mypage add column `bgtype` enum ('color', 'imageurl') default 'color' NOT NULL;
alter table tiki_mypage add column `winbgimage` varchar(255) NULL;
alter table tiki_mypage add column `winbgtype` enum ('color', 'imageurl') default 'color' NOT NULL;

# 2007-09-22 pkdille
DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='o' and name='Search stats' and url='tiki-search_stats.php' and position='1125' and section='feature_search' and perm='tiki_p_admin';
DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='o' and name='Search stats' and url='tiki-search_stats.php' and position='1125' and section='feature_search_stats' and perm='tiki_p_admin';
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Search stats','tiki-search_stats.php',1125,'feature_search_stats','tiki_p_admin','');

# 2007-09-25 mose
CREATE TABLE tiki_newsletter_included (
  nlId int(12) NOT NULL default '0',
  includedId int(12) NOT NULL default '0',
  PRIMARY KEY  (nlId,includedId)
) ENGINE=MyISAM;

# 2007-10-01 sylvieg
ALTER TABLE tiki_received_pages ADD COLUMN  structureName varchar(250) default NULL;
ALTER TABLE tiki_received_pages ADD COLUMN  parentName varchar(250) default NULL;
ALTER TABLE tiki_received_pages ADD COLUMN  page_alias varchar(250) default '';
ALTER TABLE tiki_received_pages ADD COLUMN  pos int(4) default NULL;
ALTER TABLE tiki_received_pages ADD KEY structureName (`structureName`);

#2007-10-05 sylvieg
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_watch_structure', 'Can watch structure', 'registered', 'wiki');

#2007-10-06 pkdille
INSERT INTO users_permissions (permName, permDesc, level, type, admin) VALUES ('tiki_p_admin_quicktags', 'Can admin quicktags', 'admin', 'quicktags', 'y');

DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='o' and name='Quicktags' and url='tiki-admin_quicktags.php' and position='1135' and section='' and perm='tiki_p_admin';
DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='o' and name='Quicktags' and url='tiki-admin_quicktags.php' and position='1135' and section='' and perm='tiki_p_admin_quicktags';
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','QuickTags','tiki-admin_quicktags.php',1135,'','tiki_p_admin_quicktags','');

DELETE FROM `tiki_menu_options` WHERE menuId='42' and type='r' and name='Admin' and url='tiki-admin.php' and position='1050' and section='' and perm='tiki_p_admin_quicktags' and groupname='' ;
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_admin_quicktags','');

#2007-10-08 sylvieg
ALTER TABLE tiki_menu_options DROP KEY uniq_menu;
ALTER IGNORE TABLE tiki_menu_options ADD UNIQUE uniq_menu (menuId,name(30),url(50),position,section(60),perm(50),groupname(50));

#2007-10-11 sylvieg
UPDATE tiki_user_watches SET event='structure_changed' WHERE event='structure_page_changed';

#2007-10-26 sylvieg
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_edit_menu', 'Can edit menu', 'admin', 'tiki');
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_edit_menu_option', 'Can edit menu option', 'admin', 'tiki');
UPDATE  tiki_menu_options set perm='tiki_p_edit_menu' where url='tiki-admin_menus.php';
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_edit_menu','');

#2007-10-30 pkdille
UPDATE tiki_menu_options set url='javascript:toggle(\'debugconsole\')' where url='javascript:toggle("debugconsole")';

#2007-11-07 pkdille
UPDATE `tiki_preferences` SET `name`='preset_galleries_thumb' WHERE `name`='preset_galleries_scale';

#2007-11-13 sylvieg
ALTER TABLE users_groups ADD userChoice CHAR(1) DEFAULT NULL;
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_assign_my_groups', 'A user can assign himself in groups', 'registered', 'tiki');
UPDATE  users_permissions set permName='tiki_p_subscribe_groups', permDesc='Can subscribe to groups' where permName='tiki_p_assign_my_groups';
UPDATE users_grouppermissions set permName='tiki_p_subscribe_groups' where permName='tiki_p_assign_my_groups';

#2007-11-21 nkoth on behalf of ntavares==Panora200
ALTER TABLE `users_groups` ADD `groupDefCat` int(12) DEFAULT 0;
ALTER TABLE `users_groups` ADD `groupTheme` varchar(255) DEFAULT '';

#2007-11-21 nyloth
ALTER TABLE tiki_tracker_fields ADD itemChoices text;

#2007-11-30 nkoth 
ALTER TABLE `tiki_content` ADD `contentLabel` varchar(255) NOT NULL DEFAULT '';

#2007-12-06 nkoth
UPDATE users_permissions set type='polls' where permName='tiki_p_vote_poll';
UPDATE users_permissions set type='polls', admin='y' where permName='tiki_p_admin_polls';
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_view_poll_results', 'Can view poll results', 'basic', 'polls');

#2007-12-06
ALTER TABLE `tiki_modules` DROP PRIMARY KEY;
ALTER TABLE `tiki_modules` ADD PRIMARY KEY  (name(100), position, ord, params(140));

# 2007-12-06 mose
UPDATE users_permissions set permName='tiki_p_edit_categorized' where permName='tiki_p_edit_categories';
UPDATE users_permissions set permName='tiki_p_view_categorized', permDesc='Can view categorized items' where permName='tiki_p_view_categories' and permDesc="Can browse categories";
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_view_categories', 'Can view categories', 'basic', 'category');
UPDATE users_grouppermissions set permName='tiki_p_edit_categorized' where permName='tiki_p_edit_categories';
UPDATE users_grouppermissions set permName='tiki_p_view_categorized' where permName='tiki_p_view_categories';
# INSERT INTO users_grouppermissions(groupName,permName) values ('Anonymous','tiki_p_view_categories');

#2007-12-07 sylvieg
ALTER TABLE `tiki_user_assigned_modules` DROP PRIMARY KEY , ADD PRIMARY KEY ( `name` ( 30 ) , `user` , `position` , `ord` );

#2008-01-07 sylvie
DELETE FROM tiki_preferences WHERE name='freetags_ascii_only';

#2008-01-07 nyloth
ALTER TABLE tiki_comments ADD COLUMN archived CHAR(1) DEFAULT NULL;

#2008-01-11 pkdille
UPDATE tiki_menu_options set name='Multiple Print' WHERE menuId=42 and type='o' and name='Print' and url='tiki-print_pages.php';

#2008-01-13 sylvieg
INSERT INTO users_objectpermissions (groupName, permName,objectType, objectId)  select groupName, 'tiki_p_view_categorized', objectType , objectId FROM users_objectpermissions where permName='tiki_p_view_categories';

#2008-01-15 pkdille
UPDATE tiki_menu_options set section='lang_use_db', perm='tiki_p_edit_languages' WHERE menuId=42 and type='o' and name='Edit languages' and url='tiki-edit_languages.php';

#2008-01-22 sylvieg
ALTER TABLE tiki_file_galleries ADD show_last_user char(1) default '?';
UPDATE tiki_file_galleries SET show_last_user='y' WHERE show_modified='y' AND show_last_user='?';
UPDATE tiki_file_galleries SET show_last_user=NULL WHERE show_last_user='?';
ALTER TABLE tiki_file_galleries CHANGE show_last_user show_last_user char(1) default NULL;

#2008-01-24 lphuberdeau
CREATE TABLE IF NOT EXISTS `tiki_pages_translation_bits` (
  `translation_bit_id` int(14) NOT NULL auto_increment,
  `page_id` int(14) NOT NULL,
  `version` int(8) NOT NULL,
  `source_translation_bit` int(10) NULL,
  `original_translation_bit` int(10) NULL,
  `flags` SET('critical') NOT NULL DEFAULT '',
  PRIMARY KEY (`translation_bit_id`),
  KEY(`page_id`),
  KEY(`original_translation_bit`)
);

#2008-02-05 lphuberdeau
 ALTER TABLE `tiki_pages_translation_bits` ADD INDEX ( `original_translation_bit` )  ;

