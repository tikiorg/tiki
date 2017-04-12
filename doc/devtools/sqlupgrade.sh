#!/bin/bash
# (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
# 
# All Rights Reserved. See copyright.txt for details and a complete list of authors.
# Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
# $Id$

# that script runs the last sql upgrade
# It reads db/local.php to find proper mysql info
# mose@tikiwiki.org

# Usage: sh doc/devtools/sqlupgrade.sh
# it has to be launched from tiki root dir
#
# 2010-10-04: If this script is not working for you, just use: 
# php installer/shell.php
# 

FIND='/usr/bin/find'
SED='/bin/sed'
MYSQL='/usr/bin/mysql'
PHP='/usr/bin/php'

if [ ! -x $PHP ]; then
	# It seems "which" tends to behave in unpredictable and "user-friendly" ways, so use type
	PHP=$(type -P php)
	if [ ! -x $PHP ]; then
		echo "You need PHP command line interpreter."
		exit 1
	fi
fi

if [ ! -d 'db' ]; then
	echo "You must launch this script from your (multi)tiki root dir."
	exit 0
fi

# Update old command 'installer/shell.php' to the newer one 'console.php d:u --site='
find db/ -name local.php -follow | sed -nr 's/db(\/([a-z0-9_-.]+))?\/local\.php/\2/p' | awk '{system("'$PHP' console.php -n database:update --site=" $0)}'

exit 0
