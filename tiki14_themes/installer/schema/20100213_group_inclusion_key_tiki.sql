ALTER TABLE `tiki_group_inclusion` DROP PRIMARY KEY;
ALTER TABLE `tiki_group_inclusion` ADD PRIMARY KEY (`groupName`(120),`includeGroup`(120));