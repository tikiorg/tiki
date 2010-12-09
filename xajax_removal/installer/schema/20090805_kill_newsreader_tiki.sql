DELETE FROM tiki_menu_options WHERE url = 'tiki-newsreader_servers.php';
DELETE FROM users_permissions WHERE `permName` = 'tiki_p_newsreader';
