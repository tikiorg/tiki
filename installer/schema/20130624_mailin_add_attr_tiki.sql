ALTER TABLE `tiki_mailin_accounts` add COLUMN `routing` char(1) NOT NULL default 'y';
ALTER TABLE `tiki_mailin_accounts` add COLUMN `leave_email` char(1) NOT NULL default 'n';