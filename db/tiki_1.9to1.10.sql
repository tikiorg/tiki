# $Header: /cvsroot/tikiwiki/tiki/db/tiki_1.9to1.10.sql,v 1.11 2004-07-20 19:51:57 teedog Exp $
                                                                                               
# The following script will update a tiki database from verion 1.9 to 1.10
#
# To execute this file do the following:
#
# $ mysql -f dbname < tiki_1.9to1.10.sql
#
# where dbname is the name of your tiki database.
#
# For example, if your tiki database is named tiki (not a bad choice), type:
#
# $ mysql -f tiki < tiki_1.9to1.10.sql
#
# You may execute this command as often as you like,
# and may safely ignore any error messages that appear.

INSERT IGNORE INTO tiki_preferences(name,value) VALUES ('feature_wiki_pageid','n');
INSERT IGNORE INTO users_permissions (permName, permDesc, level, type) VALUES ('tiki_p_view_wiki_history', 'Can view wiki page history', 'registered', 'wiki');

#
# Tables of the Opinion-Network
#


CREATE TABLE tiki_opnet_question (
	id INT( 10 ) NOT NULL AUTO_INCREMENT ,
	formtype INT(10) NOT NULL,
	question VARCHAR( 100 ) NOT NULL ,
PRIMARY KEY ( id ) 
);


CREATE TABLE tiki_opnet_formtype (
	id INT( 10 ) NOT NULL AUTO_INCREMENT ,
	name VARCHAR( 30 ) NOT NULL ,
	timestamp INT( 14 ) NOT NULL,
PRIMARY KEY ( id ) 
);


CREATE TABLE tiki_opnet_answer (
	id INT( 10 ) NOT NULL AUTO_INCREMENT ,
	question_id INT( 10 ) NOT NULL ,
	filledform_id INT( 10 ) NOT NULL ,
	value TEXT NOT NULL ,
PRIMARY KEY ( id ) 
);


CREATE TABLE tiki_opnet_filledform (
	id INT( 10 ) NOT NULL AUTO_INCREMENT ,
	who VARCHAR( 40 ) NOT NULL ,
	about_who VARCHAR( 40 ) NOT NULL ,
	formtype INT( 10 ) NOT NULL ,
	timestamp INT( 14 ) NOT NULL,
PRIMARY KEY ( id ) 
);

#
# Opinion-Network tables END
#