# mk_profile.sh - Generates a profile of all tiki preferences that differ from the default values
#
# Written by Dennis Heltzel - 8/3/03

if [ -z "$1" ]
then
  echo "Usage: $0 <database name>"
  exit 1
fi

DATABASE=$1

DUMP_FILE=$DATABASE-dump.sql
PROFILE_FILE=$DATABASE-profile.sql

mysql -s -u root -p $DATABASE <dump_prefs.sql |sort >$DUMP_FILE
diff default-inserts.sql $DUMP_FILE|grep ">"|sed -e "s/> INSERT IGNORE/REPLACE/" >$PROFILE_FILE
