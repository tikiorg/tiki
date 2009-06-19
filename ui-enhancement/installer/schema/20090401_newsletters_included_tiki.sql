#nyloth
ALTER TABLE `tiki_newsletter_subscriptions` ADD COLUMN `included` char(1) NOT NULL default 'n';
