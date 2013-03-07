ALTER TABLE `tiki_comments` CHANGE `user_ip` `user_ip` VARCHAR( 39 ) DEFAULT NULL ;
ALTER TABLE `tiki_history` CHANGE `ip` `ip` VARCHAR( 39 ) DEFAULT NULL ;
ALTER TABLE `tiki_pages` CHANGE `ip` `ip` VARCHAR( 39 ) DEFAULT NULL ;
ALTER TABLE `tiki_tags` CHANGE `ip` `ip` VARCHAR( 39 ) DEFAULT NULL ;
ALTER TABLE `tiki_user_votings` CHANGE `ip` `ip` VARCHAR( 39 ) DEFAULT NULL ;
