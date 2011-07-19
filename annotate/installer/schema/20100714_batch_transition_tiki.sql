ALTER TABLE `tiki_transitions` ADD COLUMN `batch` char(1) default NULL;
ALTER TABLE `tiki_transitions`ADD COLUMN `objectId` int(12) default NULL AFTER `type`;
ALTER TABLE `tiki_transitions` ADD KEY `batch` (`batch`);
ALTER TABLE `tiki_transitions` ADD KEY `objectId` (`objectId`);