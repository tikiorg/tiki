@echo off

rem tool for diffing an old tiki.sql+upgrade script against new tiki.sql.

rem --- temporary table used by this script:
set db=tikidiff

rem --- older sql + patch file to newer:
set first=tiki-1.8.sql
set patch=tiki_1.8to1.9.sql

rem --- newer sql
set second=tiki.sql

rem --- do not edit below this line!

echo --- drop db
mysqladmin -uroot -f drop %db%
echo --- create db
mysqladmin -uroot create %db%
echo --- insert %first%
mysql -uroot %db% < %first%
echo --- update %first% with %patch%
mysql -uroot -f %db% < %patch%
echo --- dump result to %first%.dump
mysqldump -uroot %db% > %first%.dump

echo --- drop db
mysqladmin -uroot -f drop %db%
echo --- create db
mysqladmin -uroot create %db%
echo --- insert %second%
mysql -uroot %db% < %second%
echo --- dump result to %second%.dump
mysqldump -uroot %db% > %second%.dump

echo --- diff %first%.dump and %second%.dump to tikidb.diff
diff %first%.dump %second%.dump > tikidb.diff
