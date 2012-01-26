UPDATE `users_permissions` SET `type` = 'user' WHERE `users_permissions`.`permName` =  'tiki_p_list_users';
UPDATE `users_permissions` SET `type` = 'tiki' WHERE `users_permissions`.`permName` =  'tiki_p_admin_notifications';
UPDATE `users_permissions` SET `type` = 'tiki' WHERE `users_permissions`.`permName` =  'tiki_p_edit_menu_option';
UPDATE `users_permissions` SET `type` = 'tiki' WHERE `users_permissions`.`permName` =  'tiki_p_edit_menu';
UPDATE `users_permissions` SET `type` = 'tiki' WHERE `users_permissions`.`permName` =  'tiki_p_admin_toolbars';
