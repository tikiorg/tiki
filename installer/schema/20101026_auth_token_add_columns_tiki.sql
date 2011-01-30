ALTER TABLE `tiki_auth_tokens` ADD `email` VARCHAR( 255 ) NOT NULL AFTER `entry`;
ALTER TABLE `tiki_auth_tokens` ADD `maxhits` INT( 10 ) NOT NULL default 1 AFTER `hits`;
