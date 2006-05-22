# $Header $

# The following script will update a tiki database from verion 1.9 to 1.9.1
# 
# To execute this file do the following:
#
# $ mysql -f dbname < tiki_1.9to1.9.1.sql
#
# where dbname is the name of your tiki database.
#
# For example, if your tiki database is named tiki, type:
#
# $ mysql -f tiki < tiki_1.9to1.9.1.sql
# 
# You may execute this command as often as you like, 
# and may safely ignore any error messages that appear.


# 2005-04-28 get synchronised with tiki.sql
ALTER  TABLE  tiki_newsletter_groups  modify  code varchar(32) default NULL;

# 2005-04-29 rv540
INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('trk_with_mirror_tables', 'n');

# 2005-05-03
UPDATE tiki_menu_options SET perm="tiki_p_view_trackers" WHERE url="tiki-list_trackers.php";

#2005-05-04 sg
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_view_tiki_calendar', 'Can view TikiWiki tools calendar', 'basic', 'calendar');
DROP TABLE IF EXISTS temp_users_grouppermissions;
CREATE TABLE temp_users_grouppermissions (
  groupName varchar(255) NOT NULL default '',
  permName varchar(30) NOT NULL default '',
  value char(1) default '',
  PRIMARY KEY  (groupName(30),permName)
) TYPE=MyISAM;
INSERT into temp_users_grouppermissions SELECT groupName, 'tiki_p_view_tiki_calendar', value FROM users_grouppermissions WHERE permName='tiki_p_view_calendar';
INSERT into users_grouppermissions SELECT * FROM temp_users_grouppermissions;
DROP TABLE temp_users_grouppermissions;

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
ALTER TABLE `tiki_user_tasks` CHANGE `user` `user` VARCHAR( 40 ) default NULL ;
ALTER TABLE `tiki_user_votings` CHANGE `user` `user` VARCHAR( 40 ) NOT NULL default '' ;
ALTER TABLE `tiki_user_watches` CHANGE `user` `user` VARCHAR( 40 ) NOT NULL default '' ;
ALTER TABLE `tiki_userfiles` CHANGE `user` `user` VARCHAR( 40 ) NOT NULL default '' ;
ALTER TABLE `tiki_userpoints` CHANGE `user` `user` VARCHAR( 40 ) default NULL ;
ALTER TABLE `tiki_webmail_contacts` CHANGE `user` `user` VARCHAR( 40 ) NOT NULL default '' ;
ALTER TABLE `tiki_webmail_messages` CHANGE `user` `user` VARCHAR( 40 ) NOT NULL default '' ;
ALTER TABLE `tiki_wiki_attachments` CHANGE `user` `user` VARCHAR( 40 ) default NULL ;

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
DROP TABLE IF EXISTS tiki_features;
DROP TABLE IF EXISTS users_score;
ALTER TABLE users_users DROP KEY score_2;
ALTER TABLE users_groups DROP groupHomeLocalized;

# missing field in primary key:
ALTER TABLE `users_objectpermissions` DROP PRIMARY KEY , ADD PRIMARY KEY ( `objectId` , `objectType` , `groupName` ( 30 ), `permName` ) ;

# 2005-12-11 - amette - correct perm for submitting link - WYSIWYCA
UPDATE tiki_menu_options SET perm="tiki_p_submit_link" WHERE url="tiki-directory_add_site.php";

# fixed a missing alter
ALTER TABLE sessions ADD expireref varchar(64) after expiry;

# fixed reserved word use in mysql 
alter table `tiki_articles` change `reads` `nbreads` int(14) default NULL ;
alter table `tiki_submissions` change `reads` `nbreads` int(14) default NULL ;

# 2006-04-13 fixing Typo - amette
UPDATE `users_permissions` SET `permDesc`='Can create user bookmarks' WHERE `permName`='tiki_p_create_bookmarks';
