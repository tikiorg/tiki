INSERT INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`) VALUES ('tiki_p_admin_importer', 'Can use the Tiki Importer', 'admin', 'tiki', 'y');
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) VALUES (42,'o','Tiki Importer','tiki-importer.php',1240,'','tiki_p_admin_importer','',0);
