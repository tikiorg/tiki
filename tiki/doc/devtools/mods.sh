#!/bin/bash

# -- conf ----------------------------------------------------------
PUBLIC=0
MODS_DIR=mods
MODS_URL=http://tikiwiki.org/mods
MODS_URL_ENC=http%3A%2F%2Ftikiwiki.org%2Fmods
# ------------------------------------------------------------------
CUT=/usr/bin/cut
SORT=/usr/bin/sort
TR=/usr/bin/tr
GREP=/bin/grep
WGET=/usr/bin/wget
AWK=/usr/bin/awk
BASENAME=/usr/bin/basename
DU=/usr/bin/du
# ------------------------------------------------------------------
OLDIR=`pwd`
cd $MODS_DIR/Packages

function showhelp {
	echo "Usage: $0 [command] <mod-name>"
	echo
	echo "  list    : lists the existing mods"
	echo "  info    : display info about a mod"
	echo "  update  : refreshes local list with remote index"
	echo "  upgrade : fetch new versions of mods and install them"
	echo "  rebuild : rebuild the local index using control files"
	echo "  install : include the mods files in tikiwiki"
	echo "  remove  : remove the mods files from tikiwiki"
	echo "  dl      : gets a new mod from remote without installing"
	echo
	exit 0
}

case $1 in
# ------------------------------------------------------------------
list)
	if [ -z $2 ]; then
		$CUT -d\' -f 2,4 00_list.txt | $TR "'" "-" | $SORT
	else
		$CUT -d\' -f 2,4 00_list.txt | $TR "'" "-" | $SORT | $GREP $2
	fi
;;
# ------------------------------------------------------------------
info)
	if [ -z $2 ]; then
		cd ..
		echo -n "Total Size: "
		$DU -sh .
	else
		echo "not implemented yet"
	fi
;;
# ------------------------------------------------------------------
update)
	$WGET -O 00_list.$MODS_URL_ENC.txt  $MODS_URL/Packages/00_list.public.txt
;;
# ------------------------------------------------------------------
upgrade)
	echo upgrade
	echo "not implemented yet"
;;
# ------------------------------------------------------------------
rebuild)
	echo rebuild
	for i in *.info.txt; do
		f=`$BASENAME $i`
	done
;;
# ------------------------------------------------------------------
install)
	echo install
	echo "not implemented yet"
;;
# ------------------------------------------------------------------
remove)
	echo remove
	echo "not implemented yet"
;;
# ------------------------------------------------------------------
dl)
	echo dl
	echo "not implemented yet"
;;
# ------------------------------------------------------------------
*)
	showhelp
;;
esac
# ------------------------------------------------------------------


cd $OLDIR

exit 0
