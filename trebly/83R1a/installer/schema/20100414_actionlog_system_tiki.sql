INSERT IGNORE INTO tiki_actionlog_conf(action, `objectType`, status) VALUES ('%', 'system', 'y');
UPDATE `tiki_actionlog_conf` SET `status`='y' WHERE `action`='login' and `objectType`='system';
UPDATE `tiki_actionlog_conf` SET `status`='y' WHERE `action`='%' and `objectType`='system';
