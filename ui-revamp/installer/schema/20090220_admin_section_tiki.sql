UPDATE `tiki_menu_options` SET `url` = '' WHERE url = 'tiki-admin.php' AND NOT perm = 'tiki_p_admin';

