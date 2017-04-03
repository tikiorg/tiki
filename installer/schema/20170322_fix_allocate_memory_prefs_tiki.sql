UPDATE `tiki_preferences` SET `value` = REPLACE(`value`, 'B', '') WHERE `name` LIKE 'allocate_memory_%';
