SET @pcant=0;
SELECT (@pcant:=count(*)) FROM users_permissions WHERE `permName` = 'tiki_p_modify_tracker_items_pending';
INSERT INTO users_permissions (`permName`, `permDesc`, level, type) VALUES ('tiki_p_modify_tracker_items_pending', 'Can change tracker pending items', 'registered', 'trackers');
INSERT INTO users_permissions (`permName`, `permDesc`, level, type) VALUES ('tiki_p_modify_tracker_items_closed', 'Can change tracker closed items', 'registered', 'trackers');

INSERT INTO users_objectpermissions (`groupName`, `permName`,`objectType`, `objectId`)  select `groupName`, 'tiki_p_modify_tracker_items_pending', `objectType` , `objectId` FROM users_objectpermissions where `permName`='tiki_p_modify_tracker_items' AND @pcant=0;
INSERT INTO users_objectpermissions (`groupName`, `permName`,`objectType`, `objectId`)  select `groupName`, 'tiki_p_modify_tracker_items_closed', `objectType` , `objectId` FROM users_objectpermissions where `permName`='tiki_p_modify_tracker_items' AND @pcant=0;
