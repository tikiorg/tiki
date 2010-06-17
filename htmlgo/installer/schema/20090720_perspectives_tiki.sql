
CREATE TABLE tiki_perspectives (
	`perspectiveId` int NOT NULL AUTO_INCREMENT,
	name varchar(100) NOT NULL,
	PRIMARY KEY( `perspectiveId` )
) ENGINE=MyISAM;

CREATE TABLE tiki_perspective_preferences (
	`perspectiveId` int NOT NULL,
	pref varchar(40) NOT NULL,
	value text,
	PRIMARY KEY( `perspectiveId`, pref )
) ENGINE=MyISAM;

