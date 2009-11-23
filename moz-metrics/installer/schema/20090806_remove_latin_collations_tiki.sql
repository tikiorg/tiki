ALTER TABLE `tiki_user_reports`
CHANGE `user` `user` VARCHAR( 200 ) NOT NULL ,
CHANGE `interval` `interval` VARCHAR( 20 ) NOT NULL ,
CHANGE `view` `view` VARCHAR( 8 ) NOT NULL ,
CHANGE `type` `type` VARCHAR( 5 ) NOT NULL;
ALTER TABLE `tiki_user_reports_cache`
CHANGE `user` `user` VARCHAR( 200 ) NOT NULL ,
CHANGE `event` `event` VARCHAR( 200 ) NOT NULL ,
CHANGE `data` `data` TEXT NOT NULL
