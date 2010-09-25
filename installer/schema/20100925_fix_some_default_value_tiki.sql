ALTER TABLE `tiki_language` MODIFY `source` text NOT NULL;
ALTER TABLE `tiki_language` MODIFY `changed` tinyint(1) DEFAULT NULL;
ALTER TABLE `tiki_user_votings` MODIFY `user` varchar(200) NOT NULL DEFAULT ''; 
