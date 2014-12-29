
#2008-09-01 lphuberdeau
DELETE FROM users_permissions WHERE `permName` IN('tiki_p_plugin_viewdetail', 'tiki_p_plugin_preview');
INSERT INTO users_permissions (`permName`, `permDesc`, level, type) VALUES ('tiki_p_plugin_viewdetail', 'Can view unapproved plugin details', 'registered', 'wiki');
INSERT INTO users_permissions (`permName`, `permDesc`, level, type) VALUES ('tiki_p_plugin_preview', 'Can execute unapproved plugin', 'registered', 'wiki');

