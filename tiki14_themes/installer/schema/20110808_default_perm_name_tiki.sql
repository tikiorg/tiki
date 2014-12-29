UPDATE tiki_tracker_fields SET permName = CONCAT('f_', fieldId) WHERE permName IS NULL;
