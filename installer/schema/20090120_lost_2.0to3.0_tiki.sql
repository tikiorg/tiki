#2008-07-24 sylvieg
SET @fgcant=0;
SELECT (@fgcant:=count(*)) FROM users_permissions WHERE permName = 'tiki_p_search_categorized';
INSERT INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_search_categorized', 'Can search on objects of this category', 'basic', 'category');
INSERT INTO `users_objectpermissions` (groupName, permName, objectType, objectId) SELECT  groupName, 'tiki_p_search_categorized', objectType , objectId FROM `users_objectpermissions` WHERE permName = 'tiki_p_view_categorized' AND @fgcant = 0;