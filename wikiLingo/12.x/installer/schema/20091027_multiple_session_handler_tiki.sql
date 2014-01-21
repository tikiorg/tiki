-- 2009-10-27 lphuberdeau
UPDATE `tiki_preferences` SET `name` = 'session_storage', `value` = 'db' WHERE `name` = 'session_db' AND `value` = 'y';
UPDATE `tiki_preferences` SET `name` = 'session_storage', `value` = 'default' WHERE `name` = 'session_db' AND `value` = 'n';
