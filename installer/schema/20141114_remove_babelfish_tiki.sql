DELETE FROM `tiki_preferences`
WHERE `name` IN ('feature_babelfish','feature_babelfish_logo');
DELETE FROM `tiki_modules`
WHERE `name` IN ('babelfish_links','babelfish_logo');
