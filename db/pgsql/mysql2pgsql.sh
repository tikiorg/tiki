#!/bin/sh
# $Id: mysql2pgsql.sh,v 1.4 2004-06-23 22:33:54 mose Exp $

set -x

if [ "$1" == "" ]; then
	DB=tiki
else
	DB=$1
fi

rm -f ${DB}_mysql.sql ${DB}_pgsql.sql psql.log
mysqldump -u root -p $DB >${DB}_mysql.sql || die
chmod 777 ${DB}_mysql.sql
perl ./mysql2pgsql.pl ${DB}_mysql.sql ${DB}_pgsql.sql || die
chmod 777 ${DB}_pgsql.sql
dropdb $DB || die
createdb $DB || die
psql -q -f ${DB}_pgsql.sql $DB >psql.log 2>&1
chmod 777 psql.log
egrep ERROR psql.log
