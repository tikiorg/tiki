#!/bin/sh
# originall written by mose@feu.org
# maintained by wolff_borg@yahoo.com.au

# HOWTO create TikiWiki-Light ?
# --------------------------
#
# ############################################################
# special operation for a lighter tikiwiki
# 
echo "Removing Thumbs.db ..." 
find -name Thumbs.db -exec rm -f {} \;

echo "Removing DB extras..."
rm -rf db/pgsql

echo "Removing Avatars..."
find img/avatars/ -type f -name '*.gif' | grep -v 000 | xargs -- rm -f

echo "Removing custom images..."
rm -rf img/custom

echo "Remove all languages except english (default)..."
find lang -type f -name language.php | grep -v "/en/" | xargs -- rm -rf

echo "Remove Galaxia..."
rm -rf lib/Galaxia

echo "Remove iCal..."
rm -rf lib/calendar/iCal

echo "Remove SOAP example..."
rm -rf lib/pear/SOAP/example

echo "Remove PDF fonts..."
find lib/pdflib/fonts -type f -name "*.afm" | grep -v php_Helvetica | grep -v php_Courier | xargs -- rm -f

echo "Remove styles..."
find templates/styles/* -type d | grep -v moreneat | xargs -- rm -rf
find styles/* -type d | grep -v moreneat | xargs -- rm -rf
find styles/ -type f -name "*.css" | grep -v moreneat | xargs -- rm -f

# ############################################################

echo "Setting directory perms..."
find -type d -exec chmod 775 {} \;

echo "Setting file perms..."
find -type f -exec chmod 664 {} \;
find -type f -name '*.sh' -exec chmod 775 {} \;

echo "Done."
