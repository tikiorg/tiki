SET @pcant=0;
SELECT (@pcant:=count(*)) FROM tiki_menu_options WHERE `position` = 1085;
INSERT INTO tiki_menu_options (`perm`) VALUES ('tiki_p_admin_modules');

INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'r','Admin','tiki-admin.php',1050,'','tiki_p_admin_modules','',0);
