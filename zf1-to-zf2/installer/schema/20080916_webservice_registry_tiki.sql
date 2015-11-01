
#2008-09-16 lphuberdeau
CREATE TABLE tiki_webservice (
	service VARCHAR(25) NOT NULL PRIMARY KEY,
	url VARCHAR(250),
	schema_version VARCHAR(5),
	schema_documentation VARCHAR(250)
) ENGINE=MyISAM ;

CREATE TABLE tiki_webservice_template (
	service VARCHAR(25) NOT NULL,
	template VARCHAR(25) NOT NULL,
	engine VARCHAR(15) NOT NULL,
	output VARCHAR(15) NOT NULL,
	content TEXT NOT NULL,
	last_modif INT,
	PRIMARY KEY( service, template )
) ENGINE=MyISAM ;

