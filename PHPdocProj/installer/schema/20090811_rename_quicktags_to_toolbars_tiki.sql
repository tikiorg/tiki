UPDATE `tiki_menu_options` SET `name` = 'Toolbars' WHERE `name` = 'QuickTags';
UPDATE `tiki_menu_options` SET `url` = 'tiki-admin_toolbars.php' WHERE `url` = 'tiki-admin_quicktags.php';
UPDATE `tiki_menu_options` SET `perm` = 'tiki_p_admin_toolbars' WHERE `perm` = 'tiki_p_admin_quicktags';
UPDATE `users_permissions` SET `permName` = 'tiki_p_admin_toolbars', `permDesc` = 'Can admin toolbars', `type` = 'toolbars' WHERE `permName` = 'tiki_p_admin_quicktags';
