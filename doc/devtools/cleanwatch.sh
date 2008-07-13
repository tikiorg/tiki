#!/bin/bash
# $Id$
# mose@tikiwiki.org

# Usage: ./doc/devtools/cleanwatch.sh <email> [multi]
# it has to be launched from tiki root dir
FIND='/usr/bin/find'
SED='/bin/sed'
MYSQL='/usr/bin/mysql'

if [ -z $1 ];then
	echo "Usage: cleanwatch.sh <email> [multi]"
	exit 0
fi 

MULTI=/${2:-''}
loc="db$MULTI/local.php"

echo -n "Removing watch for $1 ... "
eval `sed -e '/[\?#]/d' -e "s/\$\([-_a-z]*\)[[:space:]]*=[[:space:]]*\([-_a-zA-Z0-9\"'\.]*\);/\\1=\\2/" $loc`
LDBHOST=${host_tiki:-'localhost'}
LDBNAME=${dbs_tiki:-'tikiwiki'}
LDBUSER=${user_tiki:-'root'}
LDBPASS=${pass_tiki:-''}
mysql -f -h$LDBHOST -u$LDBUSER -p$LDBPASS -e "delete from tiki_user_watches where email='$1';" $LDBNAME 
#mysql -f -h$LDBHOST -u$LDBUSER -p$LDBPASS -e "select count(*) as a, email from tiki_user_watches group by email order by a" $LDBNAME 
echo "Done."

exit 0
