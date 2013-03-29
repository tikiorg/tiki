ALTER TABLE `users_groups` ADD COLUMN `expireAfter` int(14) default 0;
ALTER TABLE `users_usergroups` ADD COLUMN `created` int(14) default NULL;
ALTER TABLE  `users_groups` ADD KEY `expireAfter` (`expireAfter`);
