# 2008-10-10 lphuberdeau
ALTER TABLE users_permissions ADD COLUMN feature_check VARCHAR(50) NULL;

UPDATE users_permissions SET feature_check = 'feature_wiki' WHERE `permName` IN(
	'tiki_p_admin_wiki',
	'tiki_p_assign_perm_wiki_page',
	'tiki_p_edit',
	'tiki_p_export_wiki',
	'tiki_p_lock',
	'tiki_p_minor',
	'tiki_p_remove',
	'tiki_p_rename',
	'tiki_p_rollback',
	'tiki_p_view',
	'tiki_p_view_history',
	'tiki_p_view_source'
);
UPDATE users_permissions SET feature_check = 'wiki_feature_copyrights' WHERE `permName` = 'tiki_p_edit_copyrights';
UPDATE users_permissions SET feature_check = 'feature_wiki_structure' WHERE `permName` = 'tiki_p_edit_structures';
UPDATE users_permissions SET feature_check = 'feature_wiki_structure' WHERE `permName` = 'tiki_p_watch_structure';
UPDATE users_permissions SET feature_check = 'feature_wiki_pictures' WHERE `permName` = 'tiki_p_upload_picture';
UPDATE users_permissions SET feature_check = 'feature_wiki_templates' WHERE `permName` = 'tiki_p_use_as_template';
UPDATE users_permissions SET feature_check = 'feature_wiki_attachments' WHERE `permName` = 'tiki_p_admin_attachments';
UPDATE users_permissions SET feature_check = 'feature_wiki_attachments' WHERE `permName` = 'tiki_p_attach_files';
UPDATE users_permissions SET feature_check = 'feature_wiki_attachments' WHERE `permName` = 'tiki_p_wiki_view_attachments';
UPDATE users_permissions SET feature_check = 'feature_wiki_ratings' WHERE `permName` = 'tiki_p_admin_ratings';
UPDATE users_permissions SET feature_check = 'feature_wiki_ratings' WHERE `permName` = 'tiki_p_wiki_view_ratings';
UPDATE users_permissions SET feature_check = 'feature_wiki_ratings' WHERE `permName` = 'tiki_p_wiki_vote_ratings';
UPDATE users_permissions SET feature_check = 'feature_wiki_comments' WHERE `permName` = 'tiki_p_wiki_view_comments';

