#!/bin/sh

# tool for diffing an old tiki.sql+upgrade script against new tiki.sql.

# usage: $0   dbuser   dbpassword   /path/to/old/tiki.sql   /path/to/new/tiki.sql   /path/to/update/tiki-x.ytox.z.sql

user=$1
pass=$2
old=$3
new=$4
update=$5

db=tikisqldiff

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
mysqldump -e -c -u$user -p$pass $db > lastversion.dump


echo --- generate second diff file : new sql

echo make sure there is no old db
mysqladmin -u$user -p$pass drop $db

echo create empty db
mysqladmin -u$user -p$pass create $db

echo put in new sql
mysql -u$user -p$pass $db < $new

echo dump db to file
mysqldump -e -c -u$user -p$pass $db > newversion.dump

echo remove temporary db again
mysqladmin -u$user -p$pass drop $db

diff lastversion.dump newversion.dump > updated-vs-new.diff

exit 0
