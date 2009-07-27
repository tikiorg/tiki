ALTER TABLE `tiki_user_mail_accounts`
	ADD `imap` VARCHAR( 255 ) NOT NULL ,
	ADD `mbox` VARCHAR( 255 ) NOT NULL ,
	ADD `maildir` VARCHAR( 255 ) NOT NULL ,
	ADD `useSSL` CHAR( 1 ) NOT NULL DEFAULT 'n';
