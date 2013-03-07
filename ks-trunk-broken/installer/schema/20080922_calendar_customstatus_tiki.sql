
#2008-09-22 sylvieg
UPDATE tiki_calendar_options set `optionName`='defaulteventstatus' where `optionName`='customeventstatus';
ALTER table tiki_calendars ADD COLUMN customstatus enum('n','y') NOT NULL default 'y';

