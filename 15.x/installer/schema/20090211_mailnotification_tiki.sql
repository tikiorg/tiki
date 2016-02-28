
#2009-02-12 pkdille
#Add new perm tiki_p_admin_notifications which gives the perm to manage the mail notifications (without being admin)

INSERT INTO users_permissions (`permName`, `permDesc`, level, type) VALUES ('tiki_p_admin_notifications', 'Can admin mail notifications', 'editors', 'mail notifications');

UPDATE `tiki_menu_options` SET `perm` = 'tiki_p_admin_notifications' WHERE `menuId` = '42' AND name = 'Mail Notifications' AND url = 'tiki-admin_notifications.php';

