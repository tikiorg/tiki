# dump_prefs.sql
# This will dump the contents of the tiki_preferences file in a format that will allow you to re-create the table.
#
# Run this with:
# mysql -s -u [username] -p [database name] <dump_prefs.sql > [file to put insert statements in]
#
# You might run this on an existing database, them compare the results with the defaults in the default-inserts.sql file

SELECT concat( 'INSERT IGNORE INTO tiki_preferences(name,value) VALUES (''',name,''',''',value,''');') FROM tiki_preferences;
