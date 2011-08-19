ALTER TABLE users_usergroups DROP PRIMARY KEY;
ALTER TABLE users_usergroups ADD PRIMARY KEY (`userId`, `groupName`(225));
