# pkdille
# Fix on application menu (Menu 42). 'Contact Us' option should appear only if feature_contact AND feature_messages are enabled

UPDATE `tiki_menu_options` SET `section` = 'feature_contact,feature_messages' WHERE `menuId` = '42' AND type = 'o' AND name = 'Contact Us' AND url = 'tiki-contact.php' LIMIT 1 ;




