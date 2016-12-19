#sylvieg 2008-10-29
ALTER TABLE `tiki_tracker_items` ADD index `trackerId` (`trackerId`);
ALTER TABLE `tiki_tracker_fields` ADD index `trackerId` (`trackerId`);
ALTER TABLE `tiki_tracker_item_attachments` ADD index `itemId` (`itemId`);