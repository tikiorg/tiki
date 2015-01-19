UPDATE `tiki_preferences` SET `name` = 'theme' WHERE `name` = 'theme_active';
UPDATE `tiki_preferences` SET `name` = 'theme_option', `value` = REPLACE(`value`, '.css', '') WHERE `name` = 'style_option';
UPDATE `tiki_user_preferences` SET `value` = REPLACE(`value`, '.css', '') WHERE `prefName` = 'theme';
UPDATE `tiki_user_preferences` SET `prefName` = 'theme_option', `value` = REPLACE(`value`, '.css', '') WHERE `prefName` = 'theme-option';
UPDATE `tiki_theme_control_categs` SET `theme` = REPLACE(`theme`, '.css', '');
UPDATE `tiki_theme_control_objects` SET `theme` = REPLACE(`theme`, '.css', '');
UPDATE `tiki_theme_control_sections` SET `theme` = REPLACE(`theme`, '.css', '');
UPDATE `users_groups` SET `groupTheme` = REPLACE(`groupTheme`, '.css', '');
