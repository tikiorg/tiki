@echo off
rem $Id: convertsqls.bat 17165 2009-07-25 20:40:00Z Kissaki $

rem This script converts the tiki.sql file to db-specific ones
rem To start conversion just run it from its folder.
rem
rem If you don't have php-cli the, you can use:
rem convertsqls.bat domain.com/subdomain/

set VERSION="4.0"

rem Display help information?
if "%1" == "-h" (
	echo "Usage: convertsqls.bat <host> <tikiversion> or convertsqls.bat -h for this help"
	echo "       where <host> is the virtualhost/root/ for your tiki, IF NOT SET just runs php from the command line instead"
	echo "       and <tikiversion> is the tikiwiki version (automatically set to $VERSION if omitted)"
	goto end
)

rem  set tikiserver for remote conversation/download if specified as parameter
set TIKISERVER=""
if NOT "%1" == "" (
	set TIKISERVER=%1
)

rem set version if specified as parameter
if NOT "%2" == "" (
	set VERSION=%2
)

rem tiki.sql actually is MySQL code (our DB codebase) so just copy it for finished mysql scripts
cp ../tiki.sql ../tiki-%VERSION%-mysql.sql
cp ../tiki.sql ../tiki-%VERSION%-mysqli.sql
echo mysql scripts converted

rem do convert
if %TIKISERVER% == "" (
	rem convert locally
	echo Local run of php ...
	php -f mysql3topgsql72.php > pgsql72.sql.tmp
	echo pgsql scripts converted
	php -f mysql3tosqlite.php > sqlite.sql.tmp
	echo sqlite scripts converted
rem	php -f mysql3tosybase.php > sybase.sql.tmp
rem	echo sybase scripts converted
rem	php -f mysql3tooci8.php > oci8.sql.tmp
rem	echo oracle scripts converted
) else (
	rem convert remotely and download
	echo Running remote scripts and downloading script files ...
	wget -O pgsql72.sql.tmp "http://%TIKISERVER%/db/convertscripts/mysql3topgsql72.php?version=%VERSION%" 
	wget -O sybase.sql.tmp "http://%TIKISERVER%/db/convertscripts/mysql3tosybase.php?version=%VERSION%" 
	wget -O sqlite.sql.tmp "http://%TIKISERVER%/db/convertscripts/mysql3tosqlite.php?version=%VERSION%"
	wget -O oci8.sql.tmp "http://%TIKISERVER%/db/convertscripts/mysql3tooci8.php?version=%VERSION%" 
)

rem remove temporary output files (we don't need the output from conversion scripts)
rm -f *.sql.tmp
rem remove old converted scripts
rm -f ../tiki-%VERSION%-pgsql.sql ../tiki-%VERSION%-sybase.sql ../tiki-%VERSION%-sqlite.sql ../tiki-%VERSION%-oci8.sql

rem move the newly converted/created scripts
mv %VERSION%.to_pgsql72.sql ../tiki-%VERSION%-pgsql.sql
mv %VERSION%.to_sybase.sql ../tiki-%VERSION%-sybase.sql
mv %VERSION%.to_sqlite.sql ../tiki-%VERSION%-sqlite.sql
mv %VERSION%.to_oci8.sql ../tiki-%VERSION%-oci8.sql
echo moved the converted scripts

echo Done.

:end


