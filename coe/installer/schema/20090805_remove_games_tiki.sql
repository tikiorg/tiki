DROP TABLE IF EXISTS `tiki_games`;
DELETE FROM `tiki_menu_options` WHERE `menuId` = 42 AND `name` = 'Games' AND `url` = 'tiki-list_games.php';
DELETE FROM `users_permissions` WHERE `permName` = 'tiki_p_admin_games';
DELETE FROM `users_permissions` WHERE `permName` = 'tiki_p_play_games';
