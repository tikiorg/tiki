
@echo off
set VERSION="4.0"

echo Local run of php ...
php -f mysql3topgsql72.php > pgsql72.sql.tmp
echo pgsql scripts converted

rem rm -f *.sql.tmp

rm -f ../tiki-%VERSION%-pgsql.sql
mv %VERSION%.to_pgsql72.sql ../tiki-%VERSION%-pgsql.sql
echo moved pgsql file

pause