# $Header: /cvsroot/tikiwiki/tiki/db/tiki_1.9to1.10.sql,v 1.55 2005-11-09 15:40:49 sylvieg Exp $

# The following script will update a tiki database from verion 1.9 to 1.10
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


# 2005-08-26 / 2005-09-31: mdavey: new table tiki_events for notificationlib / tikisignal
CREATE TABLE `tiki_events` (
  `callback_type` int(1) NOT NULL default '3',
  `order` int(2) NOT NULL default '50',
  `event` varchar(200) NOT NULL default '',
  `file` varchar(200) NOT NULL default '',
  `object` varchar(200) NOT NULL default '',
  `method` varchar(200) NOT NULL default '',
  PRIMARY KEY  (`callback_type`,`order`)
) TYPE=MyISAM;

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
) TYPE=MyISAM;

# 2005-09-22: mdavey: move custom fields to new table
INSERT IGNORE INTO `tiki_registration_fields` (field, name) SELECT prefName as field, value as name FROM `tiki_user_preferences` WHERE user='CustomFields';
DELETE FROM  `tiki_user_preferences` WHERE user='CustomFields';

# 2005-09-07: rlpowell: These changes make a *huge* difference to speed of retrieval of forum threads.
ALTER TABLE tiki_comments MODIFY COLUMN `message_id` varchar(128) default NULL;
ALTER TABLE tiki_comments MODIFY COLUMN `in_reply_to` varchar(128) default NULL;
ALTER TABLE tiki_comments ADD INDEX THREADED (message_id, in_reply_to, parentId);

# 2005-09-07: rlpowell: These changes stop the mail system from repeatedly adding the same posts.
ALTER TABLE tiki_comments MODIFY COLUMN `userName` varchar(40) default NULL;
ALTER IGNORE TABLE tiki_comments ADD UNIQUE (parentId, userName, title, commentDate, message_id, in_reply_to);
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
) TYPE=MyISAM;
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

#2005-10-26 amette - quicktags per feature
# trackers
DELETE FROM tiki_quicktags WHERE taglabel='bold' AND taginsert='__text__' AND tagicon='images/ed_format_bold.gif' AND tagcategory='trackers';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('bold','__text__','images/ed_format_bold.gif','trackers');
DELETE FROM tiki_quicktags WHERE taglabel='italic' AND taginsert='\'\'text\'\'' AND tagicon='images/ed_format_italic.gif' AND tagcategory='trackers';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('italic','\'\'text\'\'','images/ed_format_italic.gif','trackers');
DELETE FROM tiki_quicktags WHERE taglabel='underline' AND taginsert='===text===' AND tagicon='images/ed_format_underline.gif' AND tagcategory='trackers';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('underline','===text===','images/ed_format_underline.gif','trackers');
DELETE FROM tiki_quicktags WHERE taglabel='table' AND taginsert='||r1c1|r1c2||r2c1|r2c2||' AND tagicon='images/insert_table.gif' AND tagcategory='trackers';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('table','||r1c1|r1c2||r2c1|r2c2||','images/insert_table.gif','trackers');
DELETE FROM tiki_quicktags WHERE taglabel='table new' AND taginsert='||r1c1|r1c2\nr2c1|r2c2||' AND tagicon='images/insert_table.gif' AND tagcategory='trackers';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('table new','||r1c1|r1c2\nr2c1|r2c2||','images/insert_table.gif','trackers');
DELETE FROM tiki_quicktags WHERE taglabel='external link' AND taginsert='[http://example.com|text]' AND tagicon='images/ed_link.gif' AND tagcategory='trackers';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('external link','[http://example.com|text]','images/ed_link.gif','trackers');
DELETE FROM tiki_quicktags WHERE taglabel='wiki link' AND taginsert='((text))' AND tagicon='images/ed_copy.gif' AND tagcategory='trackers';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('wiki link','((text))','images/ed_copy.gif','trackers');
DELETE FROM tiki_quicktags WHERE taglabel='heading1' AND taginsert='!text' AND tagicon='images/ed_custom.gif' AND tagcategory='trackers';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('heading1','!text','images/ed_custom.gif','trackers');
DELETE FROM tiki_quicktags WHERE taglabel='title bar' AND taginsert='-=text=-' AND tagicon='images/fullscreen_maximize.gif' AND tagcategory='trackers';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('title bar','-=text=-','images/fullscreen_maximize.gif','trackers');
DELETE FROM tiki_quicktags WHERE taglabel='box' AND taginsert='^text^' AND tagicon='images/ed_about.gif' AND tagcategory='trackers';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('box','^text^','images/ed_about.gif','trackers');
DELETE FROM tiki_quicktags WHERE taglabel='rss feed' AND taginsert='{rss id= }' AND tagicon='images/ico_link.gif' AND tagcategory='trackers';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('rss feed','{rss id= }','images/ico_link.gif','trackers');
DELETE FROM tiki_quicktags WHERE taglabel='dynamic content' AND taginsert='{content id= }' AND tagicon='images/book.gif' AND tagcategory='trackers';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('dynamic content','{content id= }','images/book.gif','trackers');
DELETE FROM tiki_quicktags WHERE taglabel='tagline' AND taginsert='{cookie}' AND tagicon='images/footprint.gif' AND tagcategory='trackers';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('tagline','{cookie}','images/footprint.gif','trackers');
DELETE FROM tiki_quicktags WHERE taglabel='hr' AND taginsert='---' AND tagicon='images/ed_hr.gif' AND tagcategory='trackers';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('hr','---','images/ed_hr.gif','trackers');
DELETE FROM tiki_quicktags WHERE taglabel='center text' AND taginsert='::text::' AND tagicon='images/ed_align_center.gif' AND tagcategory='trackers';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('center text','::text::','images/ed_align_center.gif','trackers');
DELETE FROM tiki_quicktags WHERE taglabel='colored text' AND taginsert='~~#FF0000:text~~' AND tagicon='images/fontfamily.gif' AND tagcategory='trackers';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('colored text','~~#FF0000:text~~','images/fontfamily.gif','trackers');
DELETE FROM tiki_quicktags WHERE taglabel='dynamic variable' AND taginsert='%text%' AND tagicon='images/book.gif' AND tagcategory='trackers';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('dynamic variable','%text%','images/book.gif','trackers');
DELETE FROM tiki_quicktags WHERE taglabel='image' AND taginsert='{img src= width= height= align= desc= link= }' AND tagicon='images/ed_image.gif' AND tagcategory='trackers';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('image','{img src= width= height= align= desc= link= }','images/ed_image.gif','trackers');
# blogs
DELETE FROM tiki_quicktags WHERE taglabel='bold' AND taginsert='__text__' AND tagicon='images/ed_format_bold.gif' AND tagcategory='blogs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('bold','__text__','images/ed_format_bold.gif','blogs');
DELETE FROM tiki_quicktags WHERE taglabel='italic' AND taginsert='\'\'text\'\'' AND tagicon='images/ed_format_italic.gif' AND tagcategory='blogs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('italic','\'\'text\'\'','images/ed_format_italic.gif','blogs');
DELETE FROM tiki_quicktags WHERE taglabel='underline' AND taginsert='===text===' AND tagicon='images/ed_format_underline.gif' AND tagcategory='blogs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('underline','===text===','images/ed_format_underline.gif','blogs');
DELETE FROM tiki_quicktags WHERE taglabel='table' AND taginsert='||r1c1|r1c2||r2c1|r2c2||' AND tagicon='images/insert_table.gif' AND tagcategory='blogs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('table','||r1c1|r1c2||r2c1|r2c2||','images/insert_table.gif','blogs');
DELETE FROM tiki_quicktags WHERE taglabel='table new' AND taginsert='||r1c1|r1c2\nr2c1|r2c2||' AND tagicon='images/insert_table.gif' AND tagcategory='blogs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('table new','||r1c1|r1c2\nr2c1|r2c2||','images/insert_table.gif','blogs');
DELETE FROM tiki_quicktags WHERE taglabel='external link' AND taginsert='[http://example.com|text]' AND tagicon='images/ed_link.gif' AND tagcategory='blogs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('external link','[http://example.com|text]','images/ed_link.gif','blogs');
DELETE FROM tiki_quicktags WHERE taglabel='wiki link' AND taginsert='((text))' AND tagicon='images/ed_copy.gif' AND tagcategory='blogs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('wiki link','((text))','images/ed_copy.gif','blogs');
DELETE FROM tiki_quicktags WHERE taglabel='heading1' AND taginsert='!text' AND tagicon='images/ed_custom.gif' AND tagcategory='blogs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('heading1','!text','images/ed_custom.gif','blogs');
DELETE FROM tiki_quicktags WHERE taglabel='title bar' AND taginsert='-=text=-' AND tagicon='images/fullscreen_maximize.gif' AND tagcategory='blogs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('title bar','-=text=-','images/fullscreen_maximize.gif','blogs');
DELETE FROM tiki_quicktags WHERE taglabel='box' AND taginsert='^text^' AND tagicon='images/ed_about.gif' AND tagcategory='blogs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('box','^text^','images/ed_about.gif','blogs');
DELETE FROM tiki_quicktags WHERE taglabel='rss feed' AND taginsert='{rss id= }' AND tagicon='images/ico_link.gif' AND tagcategory='blogs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('rss feed','{rss id= }','images/ico_link.gif','blogs');
DELETE FROM tiki_quicktags WHERE taglabel='dynamic content' AND taginsert='{content id= }' AND tagicon='images/book.gif' AND tagcategory='blogs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('dynamic content','{content id= }','images/book.gif','blogs');
DELETE FROM tiki_quicktags WHERE taglabel='tagline' AND taginsert='{cookie}' AND tagicon='images/footprint.gif' AND tagcategory='blogs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('tagline','{cookie}','images/footprint.gif','blogs');
DELETE FROM tiki_quicktags WHERE taglabel='hr' AND taginsert='---' AND tagicon='images/ed_hr.gif' AND tagcategory='blogs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('hr','---','images/ed_hr.gif','blogs');
DELETE FROM tiki_quicktags WHERE taglabel='center text' AND taginsert='::text::' AND tagicon='images/ed_align_center.gif' AND tagcategory='blogs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('center text','::text::','images/ed_align_center.gif','blogs');
DELETE FROM tiki_quicktags WHERE taglabel='colored text' AND taginsert='~~#FF0000:text~~' AND tagicon='images/fontfamily.gif' AND tagcategory='blogs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('colored text','~~#FF0000:text~~','images/fontfamily.gif','blogs');
DELETE FROM tiki_quicktags WHERE taglabel='dynamic variable' AND taginsert='%text%' AND tagicon='images/book.gif' AND tagcategory='blogs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('dynamic variable','%text%','images/book.gif','blogs');
DELETE FROM tiki_quicktags WHERE taglabel='image' AND taginsert='{img src= width= height= align= desc= link= }' AND tagicon='images/ed_image.gif' AND tagcategory='blogs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('image','{img src= width= height= align= desc= link= }','images/ed_image.gif','blogs');
#calendar
DELETE FROM tiki_quicktags WHERE taglabel='bold' AND taginsert='__text__' AND tagicon='images/ed_format_bold.gif' AND tagcategory='calendar';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('bold','__text__','images/ed_format_bold.gif','calendar');
DELETE FROM tiki_quicktags WHERE taglabel='italic' AND taginsert='\'\'text\'\'' AND tagicon='images/ed_format_italic.gif' AND tagcategory='calendar';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('italic','\'\'text\'\'','images/ed_format_italic.gif','calendar');
DELETE FROM tiki_quicktags WHERE taglabel='underline' AND taginsert='===text===' AND tagicon='images/ed_format_underline.gif' AND tagcategory='calendar';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('underline','===text===','images/ed_format_underline.gif','calendar');
DELETE FROM tiki_quicktags WHERE taglabel='table' AND taginsert='||r1c1|r1c2||r2c1|r2c2||' AND tagicon='images/insert_table.gif' AND tagcategory='calendar';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('table','||r1c1|r1c2||r2c1|r2c2||','images/insert_table.gif','calendar');
DELETE FROM tiki_quicktags WHERE taglabel='table new' AND taginsert='||r1c1|r1c2\nr2c1|r2c2||' AND tagicon='images/insert_table.gif' AND tagcategory='calendar';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('table new','||r1c1|r1c2\nr2c1|r2c2||','images/insert_table.gif','calendar');
DELETE FROM tiki_quicktags WHERE taglabel='external link' AND taginsert='[http://example.com|text]' AND tagicon='images/ed_link.gif' AND tagcategory='calendar';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('external link','[http://example.com|text]','images/ed_link.gif','calendar');
DELETE FROM tiki_quicktags WHERE taglabel='wiki link' AND taginsert='((text))' AND tagicon='images/ed_copy.gif' AND tagcategory='calendar';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('wiki link','((text))','images/ed_copy.gif','calendar');
DELETE FROM tiki_quicktags WHERE taglabel='heading1' AND taginsert='!text' AND tagicon='images/ed_custom.gif' AND tagcategory='calendar';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('heading1','!text','images/ed_custom.gif','calendar');
DELETE FROM tiki_quicktags WHERE taglabel='title bar' AND taginsert='-=text=-' AND tagicon='images/fullscreen_maximize.gif' AND tagcategory='calendar';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('title bar','-=text=-','images/fullscreen_maximize.gif','calendar');
DELETE FROM tiki_quicktags WHERE taglabel='box' AND taginsert='^text^' AND tagicon='images/ed_about.gif' AND tagcategory='calendar';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('box','^text^','images/ed_about.gif','calendar');
DELETE FROM tiki_quicktags WHERE taglabel='rss feed' AND taginsert='{rss id= }' AND tagicon='images/ico_link.gif' AND tagcategory='calendar';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('rss feed','{rss id= }','images/ico_link.gif','calendar');
DELETE FROM tiki_quicktags WHERE taglabel='dynamic content' AND taginsert='{content id= }' AND tagicon='images/book.gif' AND tagcategory='calendar';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('dynamic content','{content id= }','images/book.gif','calendar');
DELETE FROM tiki_quicktags WHERE taglabel='tagline' AND taginsert='{cookie}' AND tagicon='images/footprint.gif' AND tagcategory='calendar';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('tagline','{cookie}','images/footprint.gif','calendar');
DELETE FROM tiki_quicktags WHERE taglabel='hr' AND taginsert='---' AND tagicon='images/ed_hr.gif' AND tagcategory='calendar';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('hr','---','images/ed_hr.gif','calendar');
DELETE FROM tiki_quicktags WHERE taglabel='center text' AND taginsert='::text::' AND tagicon='images/ed_align_center.gif' AND tagcategory='calendar';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('center text','::text::','images/ed_align_center.gif','calendar');
DELETE FROM tiki_quicktags WHERE taglabel='colored text' AND taginsert='~~#FF0000:text~~' AND tagicon='images/fontfamily.gif' AND tagcategory='calendar';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('colored text','~~#FF0000:text~~','images/fontfamily.gif','calendar');
DELETE FROM tiki_quicktags WHERE taglabel='dynamic variable' AND taginsert='%text%' AND tagicon='images/book.gif' AND tagcategory='calendar';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('dynamic variable','%text%','images/book.gif','calendar');
DELETE FROM tiki_quicktags WHERE taglabel='image' AND taginsert='{img src= width= height= align= desc= link= }' AND tagicon='images/ed_image.gif' AND tagcategory='calendar';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('image','{img src= width= height= align= desc= link= }','images/ed_image.gif','calendar');
# articles
DELETE FROM tiki_quicktags WHERE taglabel='bold' AND taginsert='__text__' AND tagicon='images/ed_format_bold.gif' AND tagcategory='articles';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('bold','__text__','images/ed_format_bold.gif','articles');
DELETE FROM tiki_quicktags WHERE taglabel='italic' AND taginsert='\'\'text\'\'' AND tagicon='images/ed_format_italic.gif' AND tagcategory='articles';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('italic','\'\'text\'\'','images/ed_format_italic.gif','articles');
DELETE FROM tiki_quicktags WHERE taglabel='underline' AND taginsert='===text===' AND tagicon='images/ed_format_underline.gif' AND tagcategory='articles';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('underline','===text===','images/ed_format_underline.gif','articles');
DELETE FROM tiki_quicktags WHERE taglabel='table' AND taginsert='||r1c1|r1c2||r2c1|r2c2||' AND tagicon='images/insert_table.gif' AND tagcategory='articles';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('table','||r1c1|r1c2||r2c1|r2c2||','images/insert_table.gif','articles');
DELETE FROM tiki_quicktags WHERE taglabel='table new' AND taginsert='||r1c1|r1c2\nr2c1|r2c2||' AND tagicon='images/insert_table.gif' AND tagcategory='articles';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('table new','||r1c1|r1c2\nr2c1|r2c2||','images/insert_table.gif','articles');
DELETE FROM tiki_quicktags WHERE taglabel='external link' AND taginsert='[http://example.com|text]' AND tagicon='images/ed_link.gif' AND tagcategory='articles';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('external link','[http://example.com|text]','images/ed_link.gif','articles');
DELETE FROM tiki_quicktags WHERE taglabel='wiki link' AND taginsert='((text))' AND tagicon='images/ed_copy.gif' AND tagcategory='articles';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('wiki link','((text))','images/ed_copy.gif','articles');
DELETE FROM tiki_quicktags WHERE taglabel='heading1' AND taginsert='!text' AND tagicon='images/ed_custom.gif' AND tagcategory='articles';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('heading1','!text','images/ed_custom.gif','articles');
DELETE FROM tiki_quicktags WHERE taglabel='title bar' AND taginsert='-=text=-' AND tagicon='images/fullscreen_maximize.gif' AND tagcategory='articles';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('title bar','-=text=-','images/fullscreen_maximize.gif','articles');
DELETE FROM tiki_quicktags WHERE taglabel='box' AND taginsert='^text^' AND tagicon='images/ed_about.gif' AND tagcategory='articles';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('box','^text^','images/ed_about.gif','articles');
DELETE FROM tiki_quicktags WHERE taglabel='rss feed' AND taginsert='{rss id= }' AND tagicon='images/ico_link.gif' AND tagcategory='articles';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('rss feed','{rss id= }','images/ico_link.gif','articles');
DELETE FROM tiki_quicktags WHERE taglabel='dynamic content' AND taginsert='{content id= }' AND tagicon='images/book.gif' AND tagcategory='articles';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('dynamic content','{content id= }','images/book.gif','articles');
DELETE FROM tiki_quicktags WHERE taglabel='tagline' AND taginsert='{cookie}' AND tagicon='images/footprint.gif' AND tagcategory='articles';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('tagline','{cookie}','images/footprint.gif','articles');
DELETE FROM tiki_quicktags WHERE taglabel='hr' AND taginsert='---' AND tagicon='images/ed_hr.gif' AND tagcategory='articles';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('hr','---','images/ed_hr.gif','articles');
DELETE FROM tiki_quicktags WHERE taglabel='center text' AND taginsert='::text::' AND tagicon='images/ed_align_center.gif' AND tagcategory='articles';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('center text','::text::','images/ed_align_center.gif','articles');
DELETE FROM tiki_quicktags WHERE taglabel='colored text' AND taginsert='~~#FF0000:text~~' AND tagicon='images/fontfamily.gif' AND tagcategory='articles';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('colored text','~~#FF0000:text~~','images/fontfamily.gif','articles');
DELETE FROM tiki_quicktags WHERE taglabel='dynamic variable' AND taginsert='%text%' AND tagicon='images/book.gif' AND tagcategory='articles';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('dynamic variable','%text%','images/book.gif','articles');
DELETE FROM tiki_quicktags WHERE taglabel='image' AND taginsert='{img src= width= height= align= desc= link= }' AND tagicon='images/ed_image.gif' AND tagcategory='articles';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('image','{img src= width= height= align= desc= link= }','images/ed_image.gif','articles');
# faqs
DELETE FROM tiki_quicktags WHERE taglabel='bold' AND taginsert='__text__' AND tagicon='images/ed_format_bold.gif' AND tagcategory='faqs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('bold','__text__','images/ed_format_bold.gif','faqs');
DELETE FROM tiki_quicktags WHERE taglabel='italic' AND taginsert='\'\'text\'\'' AND tagicon='images/ed_format_italic.gif' AND tagcategory='faqs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('italic','\'\'text\'\'','images/ed_format_italic.gif','faqs');
DELETE FROM tiki_quicktags WHERE taglabel='underline' AND taginsert='===text===' AND tagicon='images/ed_format_underline.gif' AND tagcategory='faqs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('underline','===text===','images/ed_format_underline.gif','faqs');
DELETE FROM tiki_quicktags WHERE taglabel='table' AND taginsert='||r1c1|r1c2||r2c1|r2c2||' AND tagicon='images/insert_table.gif' AND tagcategory='faqs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('table','||r1c1|r1c2||r2c1|r2c2||','images/insert_table.gif','faqs');
DELETE FROM tiki_quicktags WHERE taglabel='table new' AND taginsert='||r1c1|r1c2\nr2c1|r2c2||' AND tagicon='images/insert_table.gif' AND tagcategory='faqs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('table new','||r1c1|r1c2\nr2c1|r2c2||','images/insert_table.gif','faqs');
DELETE FROM tiki_quicktags WHERE taglabel='external link' AND taginsert='[http://example.com|text]' AND tagicon='images/ed_link.gif' AND tagcategory='faqs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('external link','[http://example.com|text]','images/ed_link.gif','faqs');
DELETE FROM tiki_quicktags WHERE taglabel='wiki link' AND taginsert='((text))' AND tagicon='images/ed_copy.gif' AND tagcategory='faqs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('wiki link','((text))','images/ed_copy.gif','faqs');
DELETE FROM tiki_quicktags WHERE taglabel='heading1' AND taginsert='!text' AND tagicon='images/ed_custom.gif' AND tagcategory='faqs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('heading1','!text','images/ed_custom.gif','faqs');
DELETE FROM tiki_quicktags WHERE taglabel='title bar' AND taginsert='-=text=-' AND tagicon='images/fullscreen_maximize.gif' AND tagcategory='faqs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('title bar','-=text=-','images/fullscreen_maximize.gif','faqs');
DELETE FROM tiki_quicktags WHERE taglabel='box' AND taginsert='^text^' AND tagicon='images/ed_about.gif' AND tagcategory='faqs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('box','^text^','images/ed_about.gif','faqs');
DELETE FROM tiki_quicktags WHERE taglabel='rss feed' AND taginsert='{rss id= }' AND tagicon='images/ico_link.gif' AND tagcategory='faqs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('rss feed','{rss id= }','images/ico_link.gif','faqs');
DELETE FROM tiki_quicktags WHERE taglabel='dynamic content' AND taginsert='{content id= }' AND tagicon='images/book.gif' AND tagcategory='faqs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('dynamic content','{content id= }','images/book.gif','faqs');
DELETE FROM tiki_quicktags WHERE taglabel='tagline' AND taginsert='{cookie}' AND tagicon='images/footprint.gif' AND tagcategory='faqs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('tagline','{cookie}','images/footprint.gif','faqs');
DELETE FROM tiki_quicktags WHERE taglabel='hr' AND taginsert='---' AND tagicon='images/ed_hr.gif' AND tagcategory='faqs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('hr','---','images/ed_hr.gif','faqs');
DELETE FROM tiki_quicktags WHERE taglabel='center text' AND taginsert='::text::' AND tagicon='images/ed_align_center.gif' AND tagcategory='faqs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('center text','::text::','images/ed_align_center.gif','faqs');
DELETE FROM tiki_quicktags WHERE taglabel='colored text' AND taginsert='~~#FF0000:text~~' AND tagicon='images/fontfamily.gif' AND tagcategory='faqs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('colored text','~~#FF0000:text~~','images/fontfamily.gif','faqs');
DELETE FROM tiki_quicktags WHERE taglabel='dynamic variable' AND taginsert='%text%' AND tagicon='images/book.gif' AND tagcategory='faqs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('dynamic variable','%text%','images/book.gif','faqs');
DELETE FROM tiki_quicktags WHERE taglabel='image' AND taginsert='{img src= width= height= align= desc= link= }' AND tagicon='images/ed_image.gif' AND tagcategory='faqs';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('image','{img src= width= height= align= desc= link= }','images/ed_image.gif','faqs');
# forums
DELETE FROM tiki_quicktags WHERE taglabel='bold' AND taginsert='__text__' AND tagicon='images/ed_format_bold.gif' AND tagcategory='forums';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('bold','__text__','images/ed_format_bold.gif','forums');
DELETE FROM tiki_quicktags WHERE taglabel='italic' AND taginsert='\'\'text\'\'' AND tagicon='images/ed_format_italic.gif' AND tagcategory='forums';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('italic','\'\'text\'\'','images/ed_format_italic.gif','forums');
DELETE FROM tiki_quicktags WHERE taglabel='underline' AND taginsert='===text===' AND tagicon='images/ed_format_underline.gif' AND tagcategory='forums';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('underline','===text===','images/ed_format_underline.gif','forums');
DELETE FROM tiki_quicktags WHERE taglabel='table' AND taginsert='||r1c1|r1c2||r2c1|r2c2||' AND tagicon='images/insert_table.gif' AND tagcategory='forums';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('table','||r1c1|r1c2||r2c1|r2c2||','images/insert_table.gif','forums');
DELETE FROM tiki_quicktags WHERE taglabel='table new' AND taginsert='||r1c1|r1c2\nr2c1|r2c2||' AND tagicon='images/insert_table.gif' AND tagcategory='forums';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('table new','||r1c1|r1c2\nr2c1|r2c2||','images/insert_table.gif','forums');
DELETE FROM tiki_quicktags WHERE taglabel='external link' AND taginsert='[http://example.com|text]' AND tagicon='images/ed_link.gif' AND tagcategory='forums';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('external link','[http://example.com|text]','images/ed_link.gif','forums');
DELETE FROM tiki_quicktags WHERE taglabel='wiki link' AND taginsert='((text))' AND tagicon='images/ed_copy.gif' AND tagcategory='forums';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('wiki link','((text))','images/ed_copy.gif','forums');
DELETE FROM tiki_quicktags WHERE taglabel='heading1' AND taginsert='!text' AND tagicon='images/ed_custom.gif' AND tagcategory='forums';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('heading1','!text','images/ed_custom.gif','forums');
DELETE FROM tiki_quicktags WHERE taglabel='title bar' AND taginsert='-=text=-' AND tagicon='images/fullscreen_maximize.gif' AND tagcategory='forums';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('title bar','-=text=-','images/fullscreen_maximize.gif','forums');
DELETE FROM tiki_quicktags WHERE taglabel='box' AND taginsert='^text^' AND tagicon='images/ed_about.gif' AND tagcategory='forums';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('box','^text^','images/ed_about.gif','forums');
DELETE FROM tiki_quicktags WHERE taglabel='rss feed' AND taginsert='{rss id= }' AND tagicon='images/ico_link.gif' AND tagcategory='forums';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('rss feed','{rss id= }','images/ico_link.gif','forums');
DELETE FROM tiki_quicktags WHERE taglabel='dynamic content' AND taginsert='{content id= }' AND tagicon='images/book.gif' AND tagcategory='forums';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('dynamic content','{content id= }','images/book.gif','forums');
DELETE FROM tiki_quicktags WHERE taglabel='tagline' AND taginsert='{cookie}' AND tagicon='images/footprint.gif' AND tagcategory='forums';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('tagline','{cookie}','images/footprint.gif','forums');
DELETE FROM tiki_quicktags WHERE taglabel='hr' AND taginsert='---' AND tagicon='images/ed_hr.gif' AND tagcategory='forums';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('hr','---','images/ed_hr.gif','forums');
DELETE FROM tiki_quicktags WHERE taglabel='center text' AND taginsert='::text::' AND tagicon='images/ed_align_center.gif' AND tagcategory='forums';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('center text','::text::','images/ed_align_center.gif','forums');
DELETE FROM tiki_quicktags WHERE taglabel='colored text' AND taginsert='~~#FF0000:text~~' AND tagicon='images/fontfamily.gif' AND tagcategory='forums';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('colored text','~~#FF0000:text~~','images/fontfamily.gif','forums');
DELETE FROM tiki_quicktags WHERE taglabel='dynamic variable' AND taginsert='%text%' AND tagicon='images/book.gif' AND tagcategory='forums';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('dynamic variable','%text%','images/book.gif','forums');
DELETE FROM tiki_quicktags WHERE taglabel='image' AND taginsert='{img src= width= height= align= desc= link= }' AND tagicon='images/ed_image.gif' AND tagcategory='forums';
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('image','{img src= width= height= align= desc= link= }','images/ed_image.gif','forums');

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
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Comments','tiki-list_comments.php',1260,'feature_wiki_comments','tiki_p_admin','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Comments','tiki-list_comments.php',1260,'feature_article_comments','tiki_p_admin','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Comments','tiki-list_comments.php',1260,'feature_blog_comments','tiki_p_admin','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Comments','tiki-list_comments.php',1260,'feature_file_galleries_comments','tiki_p_admin','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Comments','tiki-list_comments.php',1260,'feature_image_galleries_comments','tiki_p_admin','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Comments','tiki-list_comments.php',1260,'feature_poll_comments','tiki_p_admin','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Comments','tiki-list_comments.php',1260,'feature_faq_comments','tiki_p_admin','');

