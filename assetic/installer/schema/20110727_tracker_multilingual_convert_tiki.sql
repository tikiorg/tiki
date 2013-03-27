
ALTER TABLE `tiki_tracker_item_fields` DROP PRIMARY KEY;
ALTER TABLE `tiki_tracker_item_fields` DROP KEY `lang`;
ALTER TABLE `tiki_tracker_item_fields` DROP COLUMN `lang`;
ALTER TABLE `tiki_tracker_item_fields` ADD PRIMARY KEY (`itemId`, `fieldId`);

ALTER TABLE `tiki_tracker_item_field_logs` DROP COLUMN `lang`;
