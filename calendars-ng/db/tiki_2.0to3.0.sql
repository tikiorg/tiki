# $Id: tiki_2.0to3.0.sql 13749 2008-07-19 23:57:28Z m_stef $

# The following script will update a tiki database from version 2.0 to 3.0
# 
# To execute this file do the following:
#
# $ mysql -f dbname < tiki_2.0to3.0.sql
#
# where dbname is the name of your tiki database.
#
# For example, if your tiki database is named tiki (not a bad choice), type:
#
# $ mysql -f tiki < tiki_2.0to3.0.sql
# 
# You may execute this command as often as you like, 
# and may safely ignore any error messages that appear.

#2008-07-24 sylvieg
SET @fgcant=0;
SELECT (@fgcant:=count(*)) FROM users_permissions WHERE permName = 'tiki_p_search_categorized';
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_search_categorized', 'Can search on objects of this category', 'basic', 'category');
INSERT INTO `users_objectpermissions` (groupName, permName, objectType, objectId) SELECT  groupName, 'tiki_p_search_categorized', objectType , objectId FROM `users_objectpermissions` WHERE permName = 'tiki_p_view_categorized' AND @fgcant = 0;

#2008-08-05 sylvieg
ALTER TABLE tiki_quicktags ADD UNIQUE KEY no_repeats(taglabel(50), taginsert(50), tagicon(100), tagcategory(50));
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('Deleted','--text--','pics/icons/text_strikethrough.png','wiki');
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('Deleted','--text--','pics/icons/text_strikethrough.png','newsletters');
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('Deleted','--text--','pics/icons/text_strikethrough.png','trackers');
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('Deleted','--text--','pics/icons/text_strikethrough.png','blogs');
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('Deleted','--text--','pics/icons/text_strikethrough.png','calendar');
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('Deleted','--text--','pics/icons/text_strikethrough.png','articles');
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('Deleted','--text--','pics/icons/text_strikethrough.png','faqs');
INSERT INTO tiki_quicktags (taglabel, taginsert, tagicon, tagcategory) VALUES ('Deleted','--text--','pics/icons/text_strikethrough.png','forums');

#2008-08-14 sept
UPDATE `tiki_menu_options` set `url`='tiki-list_file_gallery.php' where `url`='tiki-file_galleries.php';

#2008-08-14 pkdille
UPDATE `tiki_menu_options` SET `url`='tiki-admin_include_score.php' where `url`='tiki-admin_score.php';

#2008-08-16 princessxine
#2008-08-27 bitey [embiggens the feature_type column]
CREATE TABLE `tiki_feature` (
  `feature_id` mediumint(9) NOT NULL auto_increment,
  `feature_name` varchar(150) NOT NULL,
  `parent_id` mediumint(9) NOT NULL,
  `status` varchar(12) NOT NULL default 'active',
  `setting_name` varchar(50) default NULL,
  `feature_type` varchar(30) NOT NULL default 'feature',
  `template` varchar(50) default NULL,
  `permission` varchar(50) default NULL,
  `ordinal` mediumint(9) NOT NULL default '1',
  `depends_on` mediumint(9) default NULL,
  `keyword` varchar(20) default NULL,
  `feature_count` mediumint(9) NOT NULL default '0',
  `feature_path` varchar(20) NOT NULL default '0',
  PRIMARY KEY  (`feature_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

#2008-08-17 lphuberdeau
UPDATE tiki_menu_options SET section = 'feature_wiki_structure' WHERE optionId = 47;

#2008-08-22 lphuberdeau
ALTER TABLE tiki_links ADD COLUMN reltype VARCHAR(50);
CREATE TABLE tiki_semantic_tokens (
	token VARCHAR(15) PRIMARY KEY,
	label VARCHAR(25) NOT NULL,
	invert_token VARCHAR(15)
) ENGINE=MyISAM ;

#2008-08-29 lphuberdeau
INSERT INTO tiki_semantic_tokens (token, label) VALUES('alias', 'Page Alias');

#2008-08-29 lphuberdeau
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_plugin_viewdetail', 'Can view unapproved plugin details', 'editors', 'wiki');
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_plugin_preview', 'Can execute unapproved plugin', 'editors', 'wiki');
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_plugin_approve', 'Can approve plugin execution', 'editors', 'wiki');

#2008-09-01 lphuberdeau
DELETE FROM users_permissions WHERE permName IN('tiki_p_plugin_viewdetail', 'tiki_p_plugin_preview');
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_plugin_viewdetail', 'Can view unapproved plugin details', 'registered', 'wiki');
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_plugin_preview', 'Can execute unapproved plugin', 'registered', 'wiki');

#2008-09-02 sylvieg
ALTER TABLE tiki_tracker_fields ADD COLUMN descriptionIsParsed char(1) default 'n' AFTER editableBy;

#2008-09-05 lphuberdeau
ALTER TABLE tiki_feature MODIFY COLUMN keyword VARCHAR(30) NULL;

#2008-09-05 bitey
ALTER TABLE tiki_feature ADD COLUMN `tip` text NULL;

#2008-09-16  MatWho
ALTER TABLE tiki_user_mail_accounts ADD COLUMN `flagsPublic` char(1) default 'n' AFTER smtpPort;
ALTER TABLE tiki_user_mail_accounts ADD COLUMN `autoRefresh` int(4) NOT NULL default 0 AFTER flagsPublic;
ALTER TABLE tiki_webmail_messages ADD COLUMN `flaggedMsg` varchar(50) default '' AFTER isFlagged;

#2008-09-16 lphuberdeau
CREATE TABLE tiki_webservice (
	service VARCHAR(25) NOT NULL PRIMARY KEY,
	url VARCHAR(250),
	schema_version VARCHAR(5),
	schema_documentation VARCHAR(250)
) ENGINE=MyISAM ;

CREATE TABLE tiki_webservice_template (
	service VARCHAR(25) NOT NULL,
	template VARCHAR(25) NOT NULL,
	engine VARCHAR(15) NOT NULL,
	output VARCHAR(15) NOT NULL,
	content TEXT NOT NULL,
	last_modif INT,
	PRIMARY KEY( service, template )
) ENGINE=MyISAM ;

#2008-09-22 sylvieg
UPDATE tiki_calendar_options set optionName='defaulteventstatus' where optionName='customeventstatus';
ALTER table tiki_calendars ADD COLUMN customstatus enum('n','y') NOT NULL default 'y';
