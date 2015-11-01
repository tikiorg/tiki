#Some corrections and some new feature checks on permissions : http://dev.tiki.org/wish2081

UPDATE users_permissions SET feature_check = 'feature_wiki_export' WHERE `permName` = 'tiki_p_export_wiki';
UPDATE users_permissions SET feature_check = 'feature_history' WHERE `permName` = 'tiki_p_wiki_view_history';
UPDATE users_permissions SET feature_check = 'feature_wiki_attachments' WHERE `permName` = 'tiki_p_wiki_attach_files';
UPDATE users_permissions SET feature_check = 'feature_wiki_attachments' WHERE `permName` = 'tiki_p_wiki_admin_attachments';
UPDATE users_permissions SET feature_check = 'feature_wiki_ratings' WHERE `permName` = 'tiki_p_wiki_admin_ratings';
UPDATE users_permissions SET feature_check = 'feature_source' WHERE `permName` = 'tiki_p_wiki_view_source';

