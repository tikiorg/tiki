# mk_profile.sh - Generates a profile of tiki settings that differ from the default values
#
# Written by Dennis Heltzel - 8/3/03
# This script can generate SQL to make a default tiki install have the same settings as an existing database
# It uses an existing dump file to get a list of all settings, then compares it to the default values
# in default-inserts.sql and filters the differences into a profile file.

# Example usage:
# mysqldump -u root -p tiki17 >tiki17-dump.sql
# mk_profile.sh tiki17-dump.sql new_profile
#
# Output file will be called new_profile.prf, and can be added as a new profile by uploading to cvs.

if [ -z "$1" ]
then
  echo "Usage: $0 <database dump file> <profile name>"
  exit 1
fi

DUMP_FILE=$1
PROFILE_DUMP=$2.all
PROFILE_FILE=$2.prf

grep "INSERT INTO tiki_preferences" $DUMP_FILE >$PROFILE_DUMP
grep "INSERT INTO users_grouppermissions" $DUMP_FILE >>$PROFILE_DUMP
grep "INSERT INTO users_groups" $DUMP_FILE >>$PROFILE_DUMP
grep "INSERT INTO users_permissions" $DUMP_FILE >>$PROFILE_DUMP
sort -u $PROFILE_DUMP|diff default-inserts.sql -|grep ">"|sed -e "s/> INSERT/REPLACE/" >$PROFILE_FILE
