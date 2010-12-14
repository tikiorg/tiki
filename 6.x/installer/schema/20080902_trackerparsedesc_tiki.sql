
#2008-09-02 sylvieg
ALTER TABLE tiki_tracker_fields ADD COLUMN `descriptionIsParsed` char(1) default 'n' AFTER `editableBy`;

