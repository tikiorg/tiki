#!/bin/sh

TIKISERVER="tiki.osgiliath.int"

cd ..
cp tiki.sql tiki-1.8-mysql.sql

wget http://$TIKISERVER/db/convertscripts/mysql3topgsql72.php
wget http://$TIKISERVER/db/convertscripts/mysql3tosybase.php
wget http://$TIKISERVER/db/convertscripts/mysql3tosqlite.php
wget http://$TIKISERVER/db/convertscripts/mysql3tooci8.php

rm mysql3topgsql72.php
rm mysql3tosybase.php
rm mysql3tosqlite.php
rm mysql3tooci8.php

mv tiki-1.8-mysql.sql_to_pgsql72.sql tiki-1.8-pgsql.sql
mv tiki-1.8-mysql.sql_to_sybase.sql tiki-1.8-sybase.sql
mv tiki-1.8-mysql.sql_to_sqlite.sql tiki-1.8-sqlite.sql
mv tiki-1.8-mysql.sql_to_oci8.sql tiki-1.8-oci8.sql

cd convertscripts
