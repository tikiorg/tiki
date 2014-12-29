UPDATE IGNORE `tiki_preferences`
SET `tiki_preferences`.`name` = 'user_selector_threshold'
WHERE `tiki_preferences`.`name` = 'tracker_jquery_user_selector_threshold';
