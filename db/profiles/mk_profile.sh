# mk_profile.sh - Generates a profile of all tiki preferences that differ from the default values
#
# Written by Dennis Heltzel - 8/3/03
# This script can generate SQL to make a default tiki install have the same preferences as an existing database
# It uses dump_prefs.sql to generate a list of all prefs in the specified database, then compares it to the default values
# in default-inserts.sql and filters the differences into a profile.sql file.
# Depending on how your database is setup, you may need to alter the mysql command parameters.

if [ -z "$1" ]
then
  echo "Usage: $0 <database name>"
  exit 1
fi

DATABASE=$1

DUMP_FILE=$DATABASE-dump.sql
PROFILE_FILE=$DATABASE.prf

mysql -s -p $DATABASE <dump_prefs.sql |sort >$DUMP_FILE
diff default-inserts.sql $DUMP_FILE|grep ">"|sed -e "s/> INSERT \/\* IGNORE \*\//REPLACE/" >$PROFILE_FILE
