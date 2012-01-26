#pkdille 2009-10-05

ALTER TABLE `tiki_feature` CHANGE `keyword` `keyword` VARCHAR( 30 )  DEFAULT NULL;
ALTER TABLE `tiki_user_votings` CHANGE `time` `time` INT( 14 ) NOT NULL DEFAULT '0';
ALTER TABLE `tiki_sefurl_regex_out` DROP KEY `left`;
