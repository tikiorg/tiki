#!/bin/sh
# (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
# 
# All Rights Reserved. See copyright.txt for details and a complete list of authors.
# Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
# $Id$

# mods.sh
#
# shell script for developers to work on mods
# made and maintained by mose at tikiwiki.org
# 
# ---------------------------------
# configure this to match your environment

TIKIROOT="/var/www/tiki19"
REL="doc/mods"
MODS="$TIKIROOT/$REL"

# list of commands used

SH="/bin/sh"
AWK="/usr/bin/awk"
GREP="/bin/grep"
CUT="/usr/bin/cut"
TR="/usr/bin/tr"

# end of config
# ---------------------------------

if [ -z $1 ];then
	echo
	echo "Usage: $0 <package_name> [copy|diff|install]"
	echo
	echo "  copy : copy tiki files to mods"
	echo "  diff : diff tiki files with mods"
	echo "  install : copy mods files to tiki"
	echo "  without 2nd arg, returns the list of files from package in tiki tree"
	echo
	echo "  !!! note that this script only works with installed packages !!! "
	echo "  !!! (this is work in progress, use is quite limited for now) !!! "
	echo 
	echo "  check documentation on http://tikiwiki.org/TikiMods"
	echo
	exit 0
fi

INFOFILE=`$GREP -m1 "'$1'" $MODS/Installed/00_list.txt | $CUT -d',' -f'1,2' | $TR -d "'" | $TR ',' '-'`
[ -z $INFOFILE ] && echo && echo "Package '$1' not found in list of installed packages." && echo && exit 0

if [ -z $2 ]; then
	$AWK -v REL=$REL '/^files:/, /^$/ { if ($2) { print $2 } }' $MODS/Packages/$INFOFILE.info.txt
elif [ $2 = "copy" ];then
	echo "Copy from tiki to mods"
	$AWK -v REL=$REL '/^files:/, /^$/ { if ($2) { print "cp " $2 " " REL "/" $1 } }' $MODS/Packages/$INFOFILE.info.txt | $SH
	echo "Done."
elif [ $2 = "diff" ];then
	echo "Diff beetween tiki and mods"
	$AWK -v REL=$REL '/^files:/, /^$/ { if ($2) { print "diff " $2 " " REL "/" $1 } }' $MODS/Packages/$INFOFILE.info.txt | $SH
	echo "Done."
elif [ $2 = "restore" ];then
	echo "Copy from mods to tiki"
	$AWK -v REL=$REL '/^files:/, /^$/ { if ($2) { print "cp " REL "/" $1 " " $2 } }' $MODS/Packages/$INFOFILE.info.txt | $SH
	echo "Done."
fi

exit 0

# ---------------------------------
# EOF
