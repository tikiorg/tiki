#sylvieg
SET @pcant=0;
SELECT (@pcant:=count(*)) FROM users_permissions WHERE `permName` = 'tiki_p_tracker_view_comments';
INSERT INTO users_permissions (`permName`, `permDesc`, level, type) VALUES ('tiki_p_tracker_view_comments', 'Can view tracker items comments', 'basic', 'trackers');
INSERT INTO users_objectpermissions (`groupName`, `permName`,`objectType`, `objectId`)  select `groupName`, 'tiki_p_tracker_view_comments', `objectType` , `objectId` FROM users_objectpermissions where `permName`='tiki_p_view_trackers' AND @pcant=0;
