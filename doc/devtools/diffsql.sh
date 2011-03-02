#!/bin/sh
# (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
# 
# All Rights Reserved. See copyright.txt for details and a complete list of authors.
# Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
# $Id$

# tool for diffing an old tiki.sql+upgrade script against new tiki.sql.

# usage: $0   dbuser   dbpassword   /path/to/old/tiki.sql   /path/to/new/tiki.sql   /path/to/update/tiki-x.ytox.z.sql

if [ $# -ne 5 ]
then
    echo 1>&2 "Usage: $0   dbuser   dbpassword   /path/to/old/tiki.sql   /path/to/new/tiki.sql   /path/to/update/tiki-x.ytox.z.sql"
		exit 1
fi

user=$1
pass=$2
old=$3
new=$4
update=$5

db=tikisqldiff

dumpopts="--skip-opt -c"

echo --- generate first diff file : old sql upgraded

echo make sure there is no old db
mysqladmin -u$user -p$pass drop $db

echo create empty db
mysqladmin -u$user -p$pass create $db

echo put in old sql
mysql -u$user -p$pass $db < $old

echo apply update to old sql
mysql -u$user -p$pass -f $db < $update

echo dump updated db to file
mysqldump $dumpopts -u$user -p$pass $db > lastversion.dump


echo --- generate second diff file : new sql

echo make sure there is no old db
mysqladmin -u$user -p$pass drop $db

echo create empty db
mysqladmin -u$user -p$pass create $db

echo put in new sql
mysql -u$user -p$pass $db < $new

echo dump db to file
mysqldump $dumpopts -u$user -p$pass $db > newversion.dump

echo remove temporary db again
mysqladmin -u$user -p$pass drop $db

diff lastversion.dump newversion.dump > updated-vs-new.diff

exit 0
