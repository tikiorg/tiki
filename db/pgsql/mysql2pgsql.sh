#!/bin/sh
# $Id: mysql2pgsql.sh,v 1.1 2003-07-15 09:53:28 rossta Exp $

# mysql2pgsql.sh

if [ "$1" == "" ]; then
	DB=tiki
else
	DB=$1
	
mysqldump -d $DB >$DB_mysql.sql
perl mysql2pqsql.pl ${DB}_mysql.sql ${DB}_pgsql.sql
dropdb $DB
createdb $DB
psql -f ${DB}_pgsql.sql $DB | tee psql.log 2>psql.err
cat psql.err
