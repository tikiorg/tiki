#!/bin/sh
# (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
# 
# All Rights Reserved. See copyright.txt for details and a complete list of authors.
# Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
# $Id$

#
# Purpose: Update your Tiki instance to the latest version of SVN.
# This is useful to have a test/development site always up to date.
# You should not use this in a production environment.
#
# It will also add a line in your footer such as:
# Last update from SVN(4.1): Monday 21 December, 2009 07:05:20 UTC - REV 23971
#
# REV is the revision number from SVN.
# To activate your bottom bar, Admin > Look & Feel > Bottom bar (feature_bot_bar)
#
# This script is intended to be ran on a cron job with the following command:
# sh doc/devtools/svnup.sh
#
# You should also put the following line on a cron (to update your database): 
# php installer/shell.php
# 
# It's possible you may need to update your permissions with "sh setup.sh". 
# This is an interactive script so you need to set groups, etc to have in cron.
#
# If _htaccess is updated, you need to rename to .htaccess as well (or run sh htaccess.sh)
#
# To fully automate, you may also want to check the 
# Tiki Remote Instance Manage (TRIM), a combination 
# of shell and PHP scripts to install, update, backup, 
# restore and monitor (check security of) a large number 
# of Tiki installations (instances).
# http://doc.tiki.org/TRIM 
#
# TODO:
# Add option to have this information in html comments instead of visible to the public
#    <!-- Last update from SVN... -->
# Add option to run php installer/shell.php as well
# Make less last.log an option
# Document how {$svnup} is created and stored (thus, how to remove)
#

rm -f .lastup .svnrev
rm -f last.log
svn update > last.log
echo `date +%s` > .lastup
svn info --xml | grep "revision=" | head -n 1 | awk -F'\"' '{print $2}' > .svnrev

# uncomment the line below to see the list of all files updated. (ex.: if running manually)
# less last.log

exit 0
