UPDATE `tiki_preferences` SET `name` = 'theme' WHERE `name` = 'theme_active';
UPDATE `tiki_preferences` SET `name` = 'theme_option', `value` = REPLACE(`value`, '.css', '') WHERE `name` = 'style_option';
UPDATE `tiki_user_preferences` SET `value` = REPLACE(`value`, '.css', '') WHERE `prefName` = 'theme';
UPDATE `tiki_user_preferences` SET `prefName` = 'theme_option', `value` = REPLACE(`value`, '.css', '') WHERE `prefName` = 'theme-option';
