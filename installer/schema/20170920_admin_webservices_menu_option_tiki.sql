INSERT INTO `tiki_menu_options`
  (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`)
  VALUES (42,'r','Settings','tiki-admin.php',1050,'','tiki_p_admin_webservices','',0);
UPDATE `tiki_menu_options` SET `perm` = 'tiki_p_admin_webservices' WHERE `url` = 'tiki-admin_webservices.php';
