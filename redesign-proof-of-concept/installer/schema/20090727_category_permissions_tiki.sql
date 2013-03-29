# 2009-07-27 lphuberdeau

INSERT INTO users_permissions (`permName`, `permDesc`, level, type) VALUES ('tiki_p_view_category', 'Can see the category in a listing', 'basic', 'category');
INSERT INTO users_permissions (`permName`, `permDesc`, level, type) VALUES ('tiki_p_modify_object_categories', 'Can change the categories on the object', 'editors', 'tiki');
INSERT INTO users_permissions (`permName`, `permDesc`, level, type) VALUES ('tiki_p_add_object', 'Can add objects in the category', 'editors', 'category');
INSERT INTO users_permissions (`permName`, `permDesc`, level, type) VALUES ('tiki_p_remove_object', 'Can remove objects from the category', 'editors', 'category');
INSERT INTO users_permissions (`permName`, `permDesc`, level, type) VALUES ('tiki_p_create_category', 'Can create new categories', 'admin', 'category');

