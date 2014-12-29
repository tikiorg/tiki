# author: 	oeversetten
# description: 	add table tiki_areas to database schema
# purpose:	table used by perspective_binder, enlists assignments 
#		of categories to perspectives, so it have not to collect
#		assignments every time a page loads

CREATE TABLE IF NOT EXISTS `tiki_areas` (
	`categId` int(11) NOT NULL,
	`perspectives` text,
	KEY `categId` (`categId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
