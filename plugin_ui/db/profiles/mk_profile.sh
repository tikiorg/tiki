# mk_profile.sh - Generates a profile of tiki settings that differ from the default values
#
# Written by Dennis Heltzel - 8/3/03
# This script can generate SQL to make a default tiki install have the same settings as an existing database
# It uses an existing dump file to get a list of all settings, then compares it to the default values
# in default-inserts.sql and filters the differences INTO a profile file.

# Example usage:
# mysqldump -u root -p tiki17 >tiki17-dump.sql
# mk_profile.sh tiki17-dump.sql new_profile
#
# Output file will be called new_profile.prf, and can be added as a new profile by uploading to cvs.

if [ -z "$1$2" ]
then
  echo "Usage: $0 <database dump file> <profile name without extension>"
  echo "Will produce a .prf file from a database dump."
  exit 1
fi

DUMP_FILE=$1
PROFILE_DUMP=$2.all
PROFILE_FILE=$2.prf

# get all the relevant insert statements from the dump file
grep -Ei 'INSERT INTO `?tiki_preferences`?' $DUMP_FILE >$PROFILE_DUMP
grep -Ei 'INSERT INTO `?users_grouppermissions`?' $DUMP_FILE >>$PROFILE_DUMP
grep -Ei 'INSERT INTO `?users_groups`?' $DUMP_FILE >>$PROFILE_DUMP
grep -Ei 'INSERT INTO `?users_permissions`?' $DUMP_FILE >>$PROFILE_DUMP
# more configurable information
grep -Ei 'INSERT INTO `?tiki_article_types`?' $DUMP_FILE >>$PROFILE_DUMP
grep -Ei 'INSERT INTO `?tiki_banners`?' $DUMP_FILE >>$PROFILE_DUMP
grep -Ei 'INSERT INTO `?tiki_calendar_categories`?' $DUMP_FILE >>$PROFILE_DUMP
grep -Ei 'INSERT INTO `?tiki_calendar_locations`?' $DUMP_FILE >>$PROFILE_DUMP
grep -Ei 'INSERT INTO `?tiki_calendars`?' $DUMP_FILE >>$PROFILE_DUMP
grep -Ei 'INSERT INTO `?tiki_categories`?' $DUMP_FILE >>$PROFILE_DUMP
grep -Ei 'INSERT INTO `?tiki_extwiki`?' $DUMP_FILE >>$PROFILE_DUMP
grep -Ei 'INSERT INTO `?tiki_group_inclusion`?' $DUMP_FILE >>$PROFILE_DUMP
grep -Ei 'INSERT INTO `?tiki_modules`?' $DUMP_FILE >>$PROFILE_DUMP
grep -Ei 'INSERT INTO `?tiki_newsreader_servers`?' $DUMP_FILE >>$PROFILE_DUMP
grep -Ei 'INSERT INTO `?tiki_quicktags`?' $DUMP_FILE >>$PROFILE_DUMP

# Create the profile
# The first lime in the profile should be a comment that gives a brief description of the profile.
# This desc will show in the select box of the installer.
echo "# $2 profile" >$PROFILE_FILE

# Sort the insert statements, diff with the defaults and change the INSERTs to REPLACEs
sort -u $PROFILE_DUMP|diff default-inserts.sql -|grep ">"|sed -e "s/> INSERT/REPLACE/" >>$PROFILE_FILE

# cleanup
rm $PROFILE_DUMP

echo "The $PROFILE_FILE file contains the new profile. Please edit it to remove un-needed lines"
