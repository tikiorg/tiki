ALTER TABLE tiki_mailin_accounts ADD anonymous char(1) NOT NULL default 'y';

INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_mantis','n');
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_mantis_admin', 'Can admin Mantis configuration', 'admin', 'mantis');
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_mantis_view', 'Can view Mantis bugs', 'registered', 'mantis');

INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'s','Mantis','tiki-mantis-main.php',190,'feature_mantis','tiki_p_mantis_view','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','View Bugs','tiki-mantis-view_bugs.php',192,'feature_mantis','tiki_p_mantis_view','');
INSERT INTO tiki_menu_options (menuId,type,name,url,position,section,perm,groupname) VALUES (42,'o','Admin','tiki-mantis-admin.php',198,'feature_mantis','tiki_p_mantis_admin','');

ALTER TABLE tiki_tracker_fields ADD position int(4) default NULL;

ALTER TABLE tiki_tracker_item_attachments CHANGE itemId itemId int(12) NOT NULL default 0;
ALTER TABLE tiki_tracker_item_attachments ADD longdesc blob;
ALTER TABLE tiki_tracker_item_attachments ADD version varchar(40) default NULL;
ALTER TABLE tiki_trackers ADD showComments char(1) default NULL;
ALTER TABLE tiki_trackers ADD orderAttachments varchar(255) NOT NULL default 'filename,created,filesize,downloads,desc';
