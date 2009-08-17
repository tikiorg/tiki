DELETE FROM `tiki_menu_options` WHERE `menuId` = 42 AND `perm` = 'tiki_p_admin_drawings';
DELETE FROM `users_permissions` WHERE `permName` = 'tiki_p_admin_drawings';
DELETE FROM `users_permissions` WHERE `permName` = 'tiki_p_edit_drawings';
