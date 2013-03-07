ALTER TABLE `tiki_tracker_fields` MODIFY `validationMessage` varchar(255) default '' AFTER `validationParam`;
ALTER TABLE `tiki_blogs` MODIFY `show_related` char(1) default NULL AFTER `always_owner`;
