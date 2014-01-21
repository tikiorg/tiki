INSERT IGNORE INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_remove_tracker_items', 'Can remove tracker items', 'registered', 'trackers', NULL, 'feature_trackers');
INSERT IGNORE INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_remove_tracker_items_pending', 'Can remove pending tracker items', 'registered', 'trackers', NULL, 'feature_trackers');
INSERT IGNORE INTO `users_permissions` (`permName`, `permDesc`, `level`, `type`, `admin`, `feature_check`) VALUES('tiki_p_remove_tracker_items_closed', 'Can remove closed tracker items', 'registered', 'trackers', NULL, 'feature_trackers');

INSERT IGNORE INTO users_objectpermissions (`groupName`, `permName`,`objectType`, `objectId`)  select `groupName`, 'tiki_p_remove_tracker_items', `objectType` , `objectId` FROM users_objectpermissions where `permName`='tiki_p_modify_tracker_items';
INSERT IGNORE INTO users_objectpermissions (`groupName`, `permName`,`objectType`, `objectId`)  select `groupName`, 'tiki_p_remove_tracker_items_pending', `objectType` , `objectId` FROM users_objectpermissions where `permName`='tiki_p_modify_tracker_items_pending';
INSERT IGNORE INTO users_objectpermissions (`groupName`, `permName`,`objectType`, `objectId`)  select `groupName`, 'tiki_p_remove_tracker_items_closed', `objectType` , `objectId` FROM users_objectpermissions where `permName`='tiki_p_modify_tracker_items_closed';

INSERT IGNORE INTO users_grouppermissions (`groupName`, `permName`)  select `groupName`, 'tiki_p_remove_tracker_items' FROM users_objectpermissions where `permName`='tiki_p_modify_tracker_items';
INSERT IGNORE INTO users_grouppermissions (`groupName`, `permName`)  select `groupName`, 'tiki_p_remove_tracker_items_pending' FROM users_objectpermissions where `permName`='tiki_p_modify_tracker_items_pending';
INSERT IGNORE INTO users_grouppermissions (`groupName`, `permName`)  select `groupName`, 'tiki_p_remove_tracker_items_closed' FROM users_objectpermissions where `permName`='tiki_p_modify_tracker_items_closed';
