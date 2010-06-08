@echo off
rem $Id: convertsqls.bat 17165 2009-07-25 20:40:00Z Kissaki $

rem This script converts the tiki.sql file to db-specific ones
rem To start conversion just run it from its folder.
rem
rem If you don't have php-cli the, you can use:
rem convertsqls.bat domain.com/subdomain/

set VERSION="4.3"

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
	php -f mysql_to_pgsql.php > pgsql.sql.tmp
	echo pgsql scripts converted
) else (
	rem convert remotely and download
	echo Running remote scripts and downloading script files ...
	wget -O pgsql.sql.tmp "http://%TIKISERVER%/db/convertscripts/mysql_to_pgsql.php?version=%VERSION%" 
)

rem remove temporary output files (we don't need the output from conversion scripts)
rm -f *.sql.tmp
rem remove old converted scripts
rm -f ../tiki-%VERSION%-pgsql.sql

rem move the newly converted/created scripts
mv %VERSION%.to_pgsql.sql ../tiki-%VERSION%-pgsql.sql

echo moved the converted scripts

echo Done.

:end


