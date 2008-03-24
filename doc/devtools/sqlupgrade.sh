#!/bin/bash
# $Header: /cvsroot/tikiwiki/tiki/doc/devtools/sqlupgrade.sh,v 1.9 2006-05-22 17:09:08 mose Exp $
# that script runs the last sql upgrade
# It reads db/local.php to find proper mysql info
# mose@tikiwiki.org

# Usage: ./doc/devtools/sqlupgrade.sh
# it has to be launched from tiki root dir
FIND='/usr/bin/find'
SED='/bin/sed'
MYSQL='/usr/bin/mysql'

UPGRADE="tiki_1.9to1.10.sql"
if [ ! -d 'db' ]; then
	echo "You must launch this script from your (multi)tiki root dir."
	exit 0
fi

for loc in `$FIND db/ -name local.php -follow`; do
	echo -n "Upgrading from $loc ... "
	eval `sed -e '/[\?#]/d' -e "s/\$\([-_a-z]*\)[[:space:]]*=[[:space:]]*\([-_a-zA-Z0-9\"'\.:]*\);/\\1=\\2/" $loc`
	LDBHOST=${host_tiki:-'localhost'}
	LDBNAME=${dbs_tiki:-'tikiwiki'}
	LDBUSER=${user_tiki:-'root'}
	LDBPASS="${pass_tiki:-''}"
	mysql -f -h$LDBHOST -u$LDBUSER -p"$LDBPASS" $LDBNAME < db/$UPGRADE
	find temp/cache/ -type f -name '[0-9a-z]*' | xargs -- rm -rf
	echo "Done."
done

exit 0
