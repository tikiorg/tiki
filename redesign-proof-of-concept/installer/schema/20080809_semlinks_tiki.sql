
#2008-08-22 lphuberdeau
ALTER TABLE tiki_links ADD COLUMN reltype VARCHAR(50);
CREATE TABLE tiki_semantic_tokens (
	token VARCHAR(15) PRIMARY KEY,
	label VARCHAR(25) NOT NULL,
	invert_token VARCHAR(15)
) ENGINE=MyISAM ;

