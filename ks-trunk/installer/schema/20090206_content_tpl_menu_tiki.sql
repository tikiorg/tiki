SET @fgcant=0;
SELECT (@fgcant:=count(*)) FROM tiki_menu_options WHERE url = 'tiki-admin_content_templates.php';
INSERT INTO `tiki_menu_options` (`menuId`, `type`, `name`, `url`, `position`, `section`, `perm`, `groupname`, `userlevel`) SELECT 42,'o','Content Templates','tiki-admin_content_templates.php',1256,'','tiki_p_edit_content_templates','',0 FROM `tiki_menu_options` WHERE @fgcant = 0;
