# Fix on application menu (Menu 42). No space between two features !

UPDATE `tiki_menu_options` SET section = 'feature_wiki,feature_wiki_structure' WHERE `menuId` = '42' AND type = 'o' AND name = 'Structures' AND url = 'tiki-admin_structures.php' AND position = '250';
