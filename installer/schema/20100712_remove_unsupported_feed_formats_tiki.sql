UPDATE `tiki_preferences` SET `value` = IF(`value` != '2' AND `value` != 5,5,`value`) WHERE `name` = 'rssfeed_default_version';
