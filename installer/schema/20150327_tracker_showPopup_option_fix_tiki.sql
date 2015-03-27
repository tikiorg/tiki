UPDATE `tiki_tracker_options` SET `value` = '' WHERE `value` = 'n' AND `name` = 'showPopup';
UPDATE `tiki_tracker_options` SET `value` = REPLACE(`value`, 'n,', '') WHERE `value` LIKE 'n,%' AND `name` = 'showPopup';
UPDATE `tiki_tracker_options` SET `value` = 'flat' WHERE `value` = 'n' AND `name` = 'sectionFormat';
