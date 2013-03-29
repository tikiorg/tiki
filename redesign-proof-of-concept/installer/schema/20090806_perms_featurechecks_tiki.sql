-- make feature_check big enough
ALTER TABLE `users_permissions` CHANGE `feature_check` `feature_check` VARCHAR( 255 );

-- simple perm/feature matches
UPDATE `users_permissions` SET `feature_check` = 'feature_blogs' WHERE `type` = 'blogs' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_calendar' WHERE `type` = 'calendar' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_categories' WHERE `type` = 'category' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_articles' WHERE `type` = 'cms' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_comm' WHERE `type` = 'comm' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_friends' WHERE `type` = 'community' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_wiki_templates,feature_cms_templates' WHERE `type` = 'content templates' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_contribution' WHERE `type` = 'contribution' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_directory' WHERE `type` = 'directory' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_faqs' WHERE `type` = 'faqs' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_file_galleries' WHERE `type` = 'file galleries' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_forums' WHERE `type` = 'forums' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_freetags' WHERE `type` = 'freetags' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_html_pages' WHERE `type` = 'html pages' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_galleries' WHERE `type` = 'image galleries' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_maps' WHERE `type` = 'maps' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_messages' WHERE `type` = 'messu' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_newsletters' WHERE `type` = 'newsletters' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_perspective' WHERE `type` = 'perspective' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_polls' WHERE `type` = 'polls' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_quizzes' WHERE `type` = 'quizzes' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_sheet' WHERE `type` = 'sheet' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_shoutbox' WHERE `type` = 'shoutbox' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_live_support' WHERE `type` = 'support' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_surveys' WHERE `type` = 'surveys' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_tikitests' WHERE `type` = 'tikitests' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_trackers' WHERE `type` = 'trackers' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_wiki' WHERE `type` = 'wiki' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_workflow' WHERE `type` = 'workflow' AND `feature_check` IS NULL;

-- perms that relate to more than one feature
UPDATE `users_permissions`
	SET `feature_check` = 'feature_wiki_comments,feature_blog_comments,feature_blogposts_comments,feature_file_galleries_comments,feature_image_galleries_comments,feature_article_comments,feature_faq_comments,feature_poll_comments,map_comments'
	WHERE `type` = 'comments' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_webmail,feature_contacts' WHERE `type` = 'webmail' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_minichat,feature_live_support' WHERE `type` = 'chat' AND `feature_check` IS NULL;

-- user perms type covers various features
UPDATE `users_permissions` SET `feature_check` = 'feature_tasks' WHERE `type` = 'user' AND `permName` = 'tiki_p_tasks' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_tasks' WHERE `type` = 'user' AND `permName` = 'tiki_p_tasks_send' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_tasks' WHERE `type` = 'user' AND `permName` = 'tiki_p_tasks_receive' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_tasks' WHERE `type` = 'user' AND `permName` = 'tiki_p_tasks_admin' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_user_bookmarks' WHERE `type` = 'user' AND `permName` = 'tiki_p_create_bookmarks' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_user_bookmarks' WHERE `type` = 'user' AND `permName` = 'tiki_p_cache_bookmarks' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_usermenu' WHERE `type` = 'user' AND `permName` = 'tiki_p_usermenu' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_userfiles' WHERE `type` = 'user' AND `permName` = 'tiki_p_userfiles' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_notepad' WHERE `type` = 'user' AND `permName` = 'tiki_p_notepad' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_minical' WHERE `type` = 'user' AND `permName` = 'tiki_p_minical' AND `feature_check` IS NULL;
UPDATE `users_permissions` SET `feature_check` = 'feature_modulecontrols' WHERE `type` = 'user' AND `permName` = 'tiki_p_configure_modules' AND `feature_check` IS NULL;

-- perms types that don't appear to relate to features - 'mail notifications', 'menus', 'quicktags', 'tiki'

