@echo off

rem tool for diffing an old tiki.sql+upgrade script against new tiki.sql.

rem --- temporary table used by this script:
set db=tikisqldiff

rem --- older sql + patch file to newer:
set first=tiki-18.sql
set patch=tiki_1.8to1.9.sql

rem --- newer sql
set second=tiki.sql

rem --- do not edit below this line!

mysqladmin -uroot -f drop %db%
mysqladmin -uroot create %db%
mysql -uroot %db% < %first%
mysql -uroot -f %db% < %patch%
mysqldump -uroot %db% > %first%.dump

mysqladmin -uroot -f drop %db%
mysqladmin -uroot create %db%
mysql -uroot %db% < %second%
mysqldump -uroot %db% > %second%.dump

diff %first%.dump %second%.dump
