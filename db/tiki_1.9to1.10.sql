# $Header: /cvsroot/tikiwiki/tiki/db/tiki_1.9to1.10.sql,v 1.3 2004-07-02 16:05:12 teedog Exp $
                                                                                               
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