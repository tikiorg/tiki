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
# -- no you don't. NO destructive action should EVER occur in this file
#DROP TABLE IF EXISTS tiki_features;
#DROP TABLE IF EXISTS users_score;
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
