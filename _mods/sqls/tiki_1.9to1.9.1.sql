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

