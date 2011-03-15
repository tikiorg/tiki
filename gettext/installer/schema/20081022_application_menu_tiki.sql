# Fix on application menu (Menu 42). Structure option should appear only if feature_wiki AND feature_wiki_structure are active

UPDATE `tiki_menu_options` SET section = 'feature_wiki, feature_wiki_structure' WHERE `menuId` = '42' AND type = 'o' AND name = 'Structures' AND url = 'tiki-admin_structures.php' AND position = '250';
