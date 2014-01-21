#sylvieg
ALTER TABLE `tiki_freetagged_objects` CHANGE `user` `user` varchar(200) default '';
ALTER TABLE `tiki_forums_queue` ADD tags varchar(255) default NULL;
ALTER TABLE `tiki_forums_queue` ADD email varchar(255) default NULL;