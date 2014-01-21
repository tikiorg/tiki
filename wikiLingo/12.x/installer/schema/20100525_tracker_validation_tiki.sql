#2010-05-25 nkoth
ALTER TABLE tiki_tracker_fields ADD COLUMN `validation` varchar(255) default '';
ALTER TABLE tiki_tracker_fields ADD COLUMN `validationParam` varchar(255) default '';

