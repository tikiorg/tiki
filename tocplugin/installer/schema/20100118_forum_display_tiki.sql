alter table `tiki_forums` add column `topics_list_lastpost_avatar` char(1) default NULL AFTER `topics_list_lastpost_title`;
alter table `tiki_forums` add column `topics_list_author_avatar` char(1) default NULL AFTER `topics_list_author`;
