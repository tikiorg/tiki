ALTER TABLE `users_objectpermissions` DROP PRIMARY KEY;
ALTER TABLE `users_objectpermissions` ADD PRIMARY KEY `uo` (`objectId`, `objectType`, `groupName`(170),`permName`);
