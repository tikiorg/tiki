#pkdille
# Remove workspaces menu items in menu 42

DELETE FROM `tiki_menu_options` WHERE `menuId` = 42 AND `name` = 'Workspaces' AND `url` = 'tiki-workspaces-index.php' AND `section` = 'feature_workspaces' AND `perm` = 'tiki_p_view_ws';
DELETE FROM `tiki_menu_options` WHERE `menuId` = 42 AND `name` = 'Workspaces Home' AND `url` = 'tiki-workspaces-index.php' AND `section` = 'feature_workspaces' AND `perm` = 'tiki_p_view_ws';
DELETE FROM `tiki_menu_options` WHERE `menuId` = 42 AND `name` = 'My Workspaces' AND `url` = 'tiki-my-workspaces.php' AND `section` = 'feature_workspaces' AND `perm` = 'tiki_p_view_ws';
DELETE FROM `tiki_menu_options` WHERE `menuId` = 42 AND `name` = 'Manage Workspaces' AND `url` = 'tiki-manage-workspaces.php' AND `section` = 'feature_workspaces' AND `perm` = 'tiki_p_admin_ws.php';

