#wggley/sylvieg
SET @pcant=0;
SELECT (@pcant:=count(*)) FROM users_permissions WHERE `permName` = 'tiki_p_wiki_view_similar';
INSERT INTO users_permissions (`permName`, `permDesc`, level, type) VALUES ('tiki_p_wiki_view_similar', 'Can view similar wiki pages', 'registered', 'wiki');
INSERT INTO users_objectpermissions (`groupName`, `permName`,`objectType`, `objectId`)  select `groupName`, 'tiki_p_wiki_view_similar', `objectType` , `objectId` FROM users_objectpermissions where `permName`='tiki_p_view' AND @pcant=0;
