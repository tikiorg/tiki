#!/bin/bash
# $Id$
# that script runs the last sql upgrade
# It reads db/local.php to find proper mysql info
# mose@tikiwiki.org

# Usage: sh ./doc/devtools/sqlupgrade.sh
# it has to be launched from tiki root dir
FIND='/usr/bin/find'
SED='/bin/sed'
MYSQL='/usr/bin/mysql'
PHP='/usr/bin/php'

if [ ! -x $PHP ]; then
	echo "You need PHP command line interpreter."
	exit 1
fi

if [ ! -d 'db' ]; then
	echo "You must launch this script from your (multi)tiki root dir."
	exit 0
fi

find db/ -name local.php -follow | sed -nr 's/db(\/([a-z0-9_-]+))?\/local\.php/\2/p' | awk '{system("'$PHP' installer/shell.php " $0)}'

exit 0
