# $Id: tiki_2.0to3.0.sql 13749 2008-07-19 23:57:28Z m_stef $

# The following script will update a tiki database from version 2.0 to 3.0
# 
# To execute this file do the following:
#
# $ mysql -f dbname < tiki_2.0to3.0.sql
#
# where dbname is the name of your tiki database.
#
# For example, if your tiki database is named tiki (not a bad choice), type:
#
# $ mysql -f tiki < tiki_2.0to3.0.sql
# 
# You may execute this command as often as you like, 
# and may safely ignore any error messages that appear.

#2008-07-20 mstef
insert into tiki_preferences (name, value) values ('summary_rss_blogs','n');
