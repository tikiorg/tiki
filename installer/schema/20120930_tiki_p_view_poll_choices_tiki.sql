SET @pcant=0;
SELECT (@pcant:=count(*)) FROM users_permissions WHERE `permName` = 'tiki_p_view_poll_choices';
INSERT INTO users_permissions (`permName`, `permDesc`, level, type) VALUES ('tiki_p_view_poll_choices', 'Can view poll user choices', 'basic', 'polls');
INSERT INTO users_objectpermissions (`groupName`, `permName`,`objectType`, `objectId`)  select `groupName`, 'tiki_p_view_poll_choices', `objectType` , `objectId` FROM users_objectpermissions where `permName`='tiki_p_view_poll_results' AND @pcant=0;
