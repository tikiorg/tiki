#2008-09-16  MatWho
ALTER TABLE tiki_user_mail_accounts ADD COLUMN `flagsPublic` char(1) default 'n' AFTER `smtpPort`;
ALTER TABLE tiki_user_mail_accounts ADD COLUMN `autoRefresh` int(4) NOT NULL default 0 AFTER `flagsPublic`;
ALTER TABLE tiki_webmail_messages ADD COLUMN `flaggedMsg` varchar(50) default '' AFTER `isFlagged`;

#2008-09-27  MatWho
INSERT INTO users_permissions (`permName`, `permDesc`, level, type) VALUES ('tiki_p_use_group_webmail', 'Can use group webmail', 'registered', 'webmail');
INSERT INTO users_permissions (`permName`, `permDesc`, level, type) VALUES ('tiki_p_admin_group_webmail', 'Can administrate group webmail accounts', 'registered', 'webmail');
INSERT INTO users_permissions (`permName`, `permDesc`, level, type) VALUES ('tiki_p_use_personal_webmail', 'Can use personal webmail accounts', 'registered', 'webmail');
INSERT INTO users_permissions (`permName`, `permDesc`, level, type) VALUES ('tiki_p_admin_personal_webmail', 'Can administrate personal webmail accounts', 'registered', 'webmail');

