#!/bin/sh

TIKISERVER="tikigmsih"
VERSION="1.9"

cd ..
cp tiki.sql tiki-$VERSION-mysql.sql

wget http://$TIKISERVER/db/convertscripts/mysql3topgsql72.php
wget http://$TIKISERVER/db/convertscripts/mysql3tosybase.php
wget http://$TIKISERVER/db/convertscripts/mysql3tosqlite.php
wget http://$TIKISERVER/db/convertscripts/mysql3tooci8.php

rm mysql3topgsql72.php
rm mysql3tosybase.php
rm mysql3tosqlite.php
rm mysql3tooci8.php

rm -f tiki-$VERSION-pgsql.sql tiki-$VERSION-sybase.sql tiki-$VERSION-sqlite.sql tiki-$VERSION-oci8.sql

mv tiki-1.8-mysql.sql_to_pgsql72.sql tiki-$VERSION-pgsql.sql
mv tiki-1.8-mysql.sql_to_sybase.sql tiki-$VERSION-sybase.sql
mv tiki-1.8-mysql.sql_to_sqlite.sql tiki-$VERSION-sqlite.sql
mv tiki-1.8-mysql.sql_to_oci8.sql tiki-$VERSION-oci8.sql

cd convertscripts
