#!/bin/bash
# (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
#
# All Rights Reserved. See copyright.txt for details and a complete list of authors.
# Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
# $Id: update.dogfoodserver.sh 41172 2012-04-29 15:25:16Z changi67 $

# TODO: Handle local file gal and wiki_up
# TODO: update to using new Console commands

#Production
DOCROOTOLDVERSION="public_html/8x"
DOCROOTDOGFOODVERSION="publib_html/9x"

OLDMYSQLDB="changi_8x"
DOGFOODMYSQLDB="changi_9x"
#Be careful, this user need to have the right to drop the database.
MYSQLUSER="changi"
MYSQLPASS="changi"
MYSQLCOMMAND="mysql -u $MYSQLUSER -p$MYSQLPASS"
MYSQLDUMPCOMMAND="mysqldump -u $MYSQLUSER -p$MYSQLPASS"

pushd $DOCROOTDOGFOODVERSION
echo "Clear cache"
php console.php -n cache:clear
echo "Update checkout"
bash doc/devtools/svnup.sh
echo "Fix permission"
bash setup.sh mixed
echo "Drop and recreate database"
$MYSQLCOMMAND -e "drop database $DOGFOODMYSQLDB;create database $DOGFOODMYSQLDB"
echo "Populate $DOGFOODMYSQLDB with $OLDMYSQLDB data"
$MYSQLDUMPCOMMAND --single-transaction $OLDMYSQLDB | $MYSQLCOMMAND $DOGFOODMYSQLDB
echo "Upgrade schema"
php console.php -n database:update
echo "Update search index"
php console.php -n index:rebuild
echo "Update memcache prefix"
$MYSQLCOMMAND $DOGFOODMYSQLDB -e "update tiki_preferences set value = \"DOGFOODtiki_\" where name = \"memcache_prefix\";"
echo "Remove cdn"
$MYSQLCOMMAND $DOGFOODMYSQLDB -e "update tiki_preferences set value = \"\" where name = \"tiki_cdn\";"
echo "Upgrading HTACCESS"
rm .htaccess
sh htaccess.sh on
popd
