/* this removes https and also performance prefs to enable copies of production tiki's run on local or development servers */
DELETE FROM `tiki_preferences` WHERE `name` IN ('https_login','session_protected', 'tiki_cachecontrol_session', 'smarty_compilation');
DELETE FROM `tiki_preferences` WHERE `name` LIKE '%minify%';
