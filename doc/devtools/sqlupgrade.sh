#!/bin/bash
# $Header: /cvsroot/tikiwiki/tiki/doc/devtools/sqlupgrade.sh,v 1.1 2004-02-27 18:44:49 mose Exp $
# that script runs the last sql upgrade
# It reads db/local.php to find proper mysql info
# mose@tikiwiki.org

# Usage: ./doc/devtools/sqlupgrade.sh
# it has to be launched from tiki root dir

UPGRADE="tiki_1.8to1.9.sql"

eval `sed -e '/[\?#]/d' -e "s/\$\([-_a-z]*\)[[:space:]]*=[[:space:]]*\([-_a-zA-Z0-9\"'\.]*\);/\\1=\\2/" db/local.php`
LDBHOST=${host_tiki:-'localhost'}
LDBNAME=${dbs_tiki:-'tikiwiki'}
LDBUSER=${user_tiki:-'root'}
LDBPASS=${pass_tiki:-''}

mysql -f -h$LDBHOST -u$LDBUSER -p$LDBPASS $LDBNAME < db/$UPGRADE
echo "Done."

exit 0
