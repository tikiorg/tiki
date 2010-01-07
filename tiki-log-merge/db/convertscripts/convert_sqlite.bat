
@echo off
set VERSION="4.1"

echo Local run of php ...
php -f mysql_to_sqlite.php > sqlite.sql.tmp
echo sqlite scripts converted

rem rm -f *.sql.tmp

rm -f ../tiki-%VERSION%-sqlite.sql
mv %VERSION%.to_sqlite.sql ../tiki-%VERSION%-sqlite.sql
echo moved sqlite file

pause
