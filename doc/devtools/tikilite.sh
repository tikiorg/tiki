#!/bin/sh
# originall written by mose@feu.org
# maintained by wolff_borg@yahoo.com.au

# HOWTO create TikiWiki-Lite ?
# --------------------------
#
# The following are instructions on how to use the TikiLite script.
#
# The current defaults are english language and MoreNeat theme.
# These can be modified from the script.
#
#To Install
#
#   1. Check out a new installation of TikiWiki into a directory of your choice.
#   2. Move to the top of your TikiWiki installation.
#   3. Modify doc/devtools/tikilight.sh to point to set your defaults.
#   4. Execute doc/devtools/tikilight.sh - this will remove all non-core files and directories. 
#
#To Update
#
#   1. Do a cvs up in your TikiLite directory
#   2. Execute doc/devtools/tikilight.sh script again 
#
# ############################################################
# special operation for a lighter tikiwiki
#
LANG_DEF="en"
THEME_DEF="moreneat"


echo "Removing Thumbs.db ..." 
find -name Thumbs.db -exec rm -f {} \;

echo "Removing DB extras..."
rm -rf db/pgsql

echo "Removing Avatars..."
find img/avatars/ -type f -name '*.gif' | grep -v 000 | xargs -- rm -f

echo "Removing custom images..."
rm -rf img/custom

echo "Remove languages except default..."
find lang -type f -name language.php | grep -v "/$LANG_DEF/" | xargs -- rm -rf

echo "Remove Galaxia..."
rm -rf lib/Galaxia

echo "Remove iCal..."
rm -rf lib/calendar/iCal

echo "Remove SOAP example..."
rm -rf lib/pear/SOAP/example

echo "Remove PDF fonts..."
find lib/pdflib/fonts -type f -name "*.afm" | grep -v php_Helvetica | grep -v php_Courier | xargs -- rm -f

echo "Remove styles except default..."
find templates/styles/* -type d | grep -v $THEME_DEF | xargs -- rm -rf
find styles/* -type d | grep -v $THEME_DEF | xargs -- rm -rf
find styles/ -type f -name "*.css" | grep -v $THEME_DEF | xargs -- rm -f

# ############################################################

echo "Setting directory perms..."
find -type d -exec chmod 775 {} \;

echo "Setting file perms..."
find -type f -exec chmod 664 {} \;
find -type f -name '*.sh' -exec chmod 775 {} \;

echo "Done."
