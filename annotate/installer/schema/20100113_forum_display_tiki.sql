alter table `tiki_forums` add column `topics_list_lastpost_title` char(1) default NULL AFTER `topics_list_lastpost`;
update `tiki_forums` set `topics_list_lastpost_title`=`topics_list_lastpost`;
