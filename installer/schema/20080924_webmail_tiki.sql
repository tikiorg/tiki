#2008-09-16  MatWho
ALTER TABLE tiki_user_mail_accounts ADD COLUMN `flagsPublic` char(1) default 'n' AFTER smtpPort;
ALTER TABLE tiki_user_mail_accounts ADD COLUMN `autoRefresh` int(4) NOT NULL default 0 AFTER flagsPublic;
ALTER TABLE tiki_webmail_messages ADD COLUMN `flaggedMsg` varchar(50) default '' AFTER isFlagged;
