#!/bin/bash
# (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
#
# All Rights Reserved. See copyright.txt for details and a complete list of authors.
# Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
# $Id: update.dogfoodserver.sh 41172 2012-04-29 15:25:16Z changi67 $

# TODO: Handle local file gal and wiki_up
# TODO: update to using new Console commands

#Production
if [ -z "$TIKI_DBHOST" ]; then
echo -n Enter the Tiki DB host:
read TIKI_DBHOST
fi

if [ -z "$TIKI_DBNAME" ]; then
echo -n Enter the Tiki DB name:
read TIKI_DBNAME
fi

if [ -z "$TIKI_DBNAME" ] ; then
echo DB name can not be emtpy.
exit 1
fi

if [ -z "$TIKI_DBUSER" ]; then
echo -n Enter the Tiki DB user:
read TIKI_DBUSER
fi

if [ -z "$TIKI_DBPASSWD" ]; then
echo -n Enter the Tiki DB password:
read TIKI_DBPASSWD
fi

# Building auxiliars
[ -z "$TIKI_DBUSER" ]  && db_user='' ||  db_user="-u $TIKI_DBUSER"
[ -z "$TIKI_DBPASSWD" ] && db_passwd='' || db_passwd="-p$TIKI_DBPASSWD"
[ -z "$TIKI_DBHOST" ] && db_host='localhost' || db_host="-h $TIKI_DBHOST"

time=$(date +%Y%m%d%H%M%S)
filename=$TIKI_DBNAME-$time.sql
mysql_command="mysqldump $db_user $db_passwd $db_host --single-transaction  $TIKI_DBNAME"
$mysql_command > $filename
[ $? -eq 0 ] &&  echo Please copy the  $filename file to your new deployment and import it with tiki.import tool
