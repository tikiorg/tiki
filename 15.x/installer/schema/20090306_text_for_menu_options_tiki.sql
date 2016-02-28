#sylvieg
ALTER TABLE `tiki_menu_options` DROP KEY `uniq_menu`; 
ALTER TABLE `tiki_menu_options` CHANGE `section` `section` text default NULL;
ALTER TABLE `tiki_menu_options` CHANGE `perm` `perm` text default NULL;
ALTER TABLE `tiki_menu_options` CHANGE `groupname` `groupname` text default NULL;
ALTER TABLE `tiki_menu_options` ADD UNIQUE KEY `uniq_menu` (`menuId`,`name`(30),`url`(50),`position`,`section`(60),`perm`(50),`groupname`(50));
