#!/bin/sh
# originall written by mose@feu.org
# maintained by wolff_borg@yahoo.com.au

# ############################################################
#
# HOWTO create Tikiwiki-Lite ?
# --------------------------
#
# The following are instructions on how to use the TikiLite script.
#
# The current defaults are english language and MoreNeat theme.
# These can be modified from the script.
#
#To Install
#
#   1. Check out a new installation of Tikiwiki into a directory of your choice.
#   2. Move to the top of your Tikiwiki installation.
#   3. Modify doc/devtools/tikilite.sh to point to set your defaults.
#   4. Execute doc/devtools/tikilite.sh - this will remove all non-core files and directories. 
#
#To Update
#
#   1. Do a cvs up in your TikiLite directory
#   2. Execute doc/devtools/tikilite.sh script again 
#
# ############################################################
#
# HOWTO modify this file
# ----------------------
#
# The structure is faily simple, removals are done on a per feature basis,
# with an indication of dependancies.
#
# Any dependancies found in multiple features, should be moved to the bottom
# of the file.
#
# Whether you are fixing a feature, creating a new feature or uncommenting
# a disabled feature, the standard should remain the same.
#
# ############################################################

LANG_DEF="en"
THEME_DEF="moreneat"

echo "Removing duplicate Date lib..."
rm -rf lib/Date

echo "Removing AdoDB tests..."
rm -rf lib/adodb/tests

echo

echo "Removing DB extras..."
rm -rf db/oracle
rm -rf db/pgsql

echo "Removing Avatars..."
find img/avatars/ -type f -name '*.gif' | grep -v 000 | xargs -- rm -f

# Used in the spell checking
# Had DB dependancies that need to be fixed
# Needs checking for sql tables in DB
echo "Removing Bablotron..."
# Dependancies on PEAR::DB
rm -rf lib/bablotron.php

echo "Removing Languages except default..."
find lang/* -type d | grep -v "/$LANG_DEF" | grep -v CVS | xargs -- rm -rf

echo "Removing iCal..."
rm -rf lib/calendar/iCal

echo "Removing jsCalendar..."
rm -rf lib/jscalendar

#echo "Removing PDF fonts..."
#find lib/pdflib/fonts -type f -name "*.afm" | grep -v php_Helvetica | grep -v php_Courier | xargs -- rm -f

echo "Removing PHPOpenTracker..."
# Dependancies on PEAR::DB
rm -rf lib/phpOpenTracker

# Couldn't find any reference to these files anywhere?
echo "Removing Popups..."
rm -rf popups

echo "Removing Styles except default..."
find styles/* -type d | grep -v $THEME_DEF | grep -v CVS | xargs -- rm -rf
find styles/ -type f -name "*.css" | grep -v $THEME_DEF | xargs -- rm -f
find templates/styles/* -type d | grep -v $THEME_DEF | xargs -- rm -rf
rm -rf styles/README.matrix-theme

echo "Removing IRC..."
rm -rf var/log/irc
rm -rf tiki-view_irc.php
rm -rf lib/irc
rm -rf templates/tiki-view_irc.tpl

echo "Removing SQL..."
# Dependancies on PEAR::DB
rm -rf lib/wiki-plugins/wikiplugin_sql.php

echo "Removing Webmail..."
rm -rf tiki-admin_include_webmail.php
rm -rf tiki-webmail.php
rm -rf tiki-webmail_contacts.php
rm -rf tiki-webmail_download_attachment.php
rm -rf img/icons/admin_webmail.png
rm -rf img/mytiki/webmail.gif
rm -rf img/webmail
rm -rf templates/tiki-admin-include-webmail.tpl
rm -rf templates/tiki-webmail.tpl
rm -rf templates/tiki-webmail_contacts.tpl
rm -rf lib/webmail
rm -rf temp/mail_attachs

echo "Removing WS Server..."
rm -rf lib/pear/SOAP
rm -rf tiki-ws_client.php
rm -rf tiki-ws_server.php

echo "Removing Tests..."
rm -rf tiki-tests.php
rm -rf tests

echo "Removing TikiMovies..."
rm -rf tiki-listmovies.php
rm -rf templates/tiki-listmovies.tpl
rm -rf tikimovies

# ############################################################
# Dependancies

echo "Removing PEAR::DB..."
rm -rf lib/pear/DB
rm -rf lib/pear/DB.php


# ############################################################

echo "Setting directory perms..."
find -type d -exec chmod 775 {} \;

echo "Setting file perms..."
find -type f -exec chmod 664 {} \;
find -type f -name '*.sh' -exec chmod 775 {} \;

echo "Done."
