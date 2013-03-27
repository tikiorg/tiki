UPDATE `users_permissions` SET `feature_check` = REPLACE(feature_check, 'feature_blog_comments,', '')  WHERE `feature_check` LIKE '%feature_blog_comments%';
DELETE FROM `tiki_menu_options` WHERE `url` = 'tiki-list_comments.php' AND `position` = 1260 AND `section` = 'feature_blog_comments';
