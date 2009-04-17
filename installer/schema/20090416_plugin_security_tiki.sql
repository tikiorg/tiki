#2009-04-16 lphuberdeau
CREATE TABLE tiki_plugin_security (
	fingerprint VARCHAR(200) NOT NULL PRIMARY KEY,
	status VARCHAR(10) NOT NULL,
	approval_by VARCHAR(200) NULL,
	last_update TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	last_objectType VARCHAR(20) NOT NULL,
	last_objectId VARCHAR(200) NOT NULL,
	KEY last_object (last_objectType, last_objectId)
);

