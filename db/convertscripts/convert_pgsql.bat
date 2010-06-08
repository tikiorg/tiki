
@echo off
set VERSION="4.3"

echo Local run of php ...
php -f mysql_to_pgsql.php > pgsql.sql.tmp
echo pgsql scripts converted

rem rm -f *.sql.tmp

rm -f ../tiki-%VERSION%-pgsql.sql
mv %VERSION%.to_pgsql.sql ../tiki-%VERSION%-pgsql.sql
echo moved pgsql file

pause
