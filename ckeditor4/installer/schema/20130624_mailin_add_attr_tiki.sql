ALTER TABLE `tiki_mailin_accounts` add COLUMN `routing` char(1) NULL AFTER `attachments`;
ALTER TABLE `tiki_mailin_accounts` add COLUMN `leave_email` char(1) NULL AFTER `respond_email`;