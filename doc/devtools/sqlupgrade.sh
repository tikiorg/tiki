#!/bin/bash
# $Header: /cvsroot/tikiwiki/tiki/doc/devtools/sqlupgrade.sh,v 1.2 2004-06-17 00:19:13 mose Exp $
# that script runs the last sql upgrade
# It reads db/local.php to find proper mysql info
# mose@tikiwiki.org

# Usage: ./doc/devtools/sqlupgrade.sh
# it has to be launched from tiki root dir
FIND='/usr/bin/find'
SED='/bin/sed'
MYSQL='/usr/bin/mysql'

UPGRADE="tiki_1.8to1.9.sql"
if [ ! -d 'db' ]; then
	echo "You must launch that script from your (multi)tiki root dir."
	exit 0
fi

for loc in `$FIND db/ -name local.php -follow`; do
	echo -n "Upgrading fron $loc ... "
	eval `sed -e '/[\?#]/d' -e "s/\$\([-_a-z]*\)[[:space:]]*=[[:space:]]*\([-_a-zA-Z0-9\"'\.]*\);/\\1=\\2/" $loc`
	LDBHOST=${host_tiki:-'localhost'}
	LDBNAME=${dbs_tiki:-'tikiwiki'}
	LDBUSER=${user_tiki:-'root'}
	LDBPASS=${pass_tiki:-''}
	mysql -f -h$LDBHOST -u$LDBUSER -p$LDBPASS $LDBNAME < db/$UPGRADE
	echo "Done."
done

exit 0
