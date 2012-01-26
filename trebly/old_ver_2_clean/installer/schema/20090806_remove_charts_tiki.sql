DELETE FROM `tiki_live_support_modules` WHERE `name` = 'charts';
DELETE FROM `tiki_menu_options` WHERE `menuId`= 42 AND `section` = 'feature_charts';
DELETE FROM `users_permissions` WHERE `type` = 'charts';
DELETE FROM `tiki_sefurl_regex_out` WHERE `type` = 'chart';
