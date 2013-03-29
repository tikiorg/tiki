#bbenamran
# Create recurrence table on calendar items model, adding rules of recurrence
CREATE TABLE tiki_calendar_recurrence (
  `recurrenceId` int(14) NOT NULL auto_increment,
  `calendarId` int(14) NOT NULL default '0',
  start int(4) NOT NULL default '0',
  end int(4) NOT NULL default '2359',
  allday tinyint(1) NOT NULL default '0',
  `locationId` int(14) default NULL,
  `categoryId` int(14) default NULL,
  `nlId` int(12) NOT NULL default '0',
  priority enum('1','2','3','4','5','6','7','8','9') NOT NULL default '1',
  status enum('0','1','2') NOT NULL default '0',
  url varchar(255) default NULL,
  lang char(16) NOT NULL default 'en',
  name varchar(255) NOT NULL default '',
  description blob,
  weekly tinyint(1) default '0',
  weekday tinyint(1),
  monthly tinyint(1) default '0',
  `dayOfMonth` int(2),
  yearly tinyint(1) default '0',
  `dateOfYear` int(4),
  `nbRecurrences` int(8),
  `startPeriod` int(14),
  `endPeriod` int(14),
  user varchar(200) default '',
  created int(14) NOT NULL default '0',
  lastmodif int(14) NOT NULL default '0',
  PRIMARY KEY (`recurrenceId`),
  KEY `calendarId` (`calendarId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

# Add reference to recurrence table in calendar items table
ALTER TABLE `tiki_calendar_items`
	ADD `recurrenceId` int(14);
ALTER TABLE tiki_calendar_items
	ADD CONSTRAINT fk_calitems_recurrence
		FOREIGN KEY (`recurrenceId`)
		REFERENCES tiki_calendar_recurrence(`recurrenceId`)
	ON UPDATE CASCADE	
	ON DELETE SET NULL;	

# Has a recurrent event been manually changed ?
ALTER TABLE `tiki_calendar_items`
	ADD changed tinyint(1) DEFAULT '0';