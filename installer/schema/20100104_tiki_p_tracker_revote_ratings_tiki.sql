SET @pcant=0;
SELECT (@pcant:=count(*)) FROM users_permissions WHERE `permName` = 'tiki_p_tracker_revote_ratings';
INSERT INTO users_permissions (`permName`, `permDesc`, level, type) VALUES ('tiki_p_tracker_revote_ratings', 'Can re-vote a rating for tracker items', 'registered', 'trackers');
INSERT INTO users_objectpermissions (`groupName`, `permName`,`objectType`, `objectId`)  select `groupName`, 'tiki_p_tracker_revote_ratings', `objectType` , `objectId` FROM users_objectpermissions where `permName`='tiki_p_tracker_vote_ratings' AND @pcant=0;

