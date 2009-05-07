#!/bin/sh
# $Id$
#
# ==========================================================================
# IMPORTANT NOTE : This script must NOT be called directly !!!
#   It is used by the main release script (doc/devtools/release.php)
#   To get the Tiki release HOWTO, try: php doc/devtools/release.php --howto
# ==========================================================================
#

SVNROOT="https://tikiwiki.svn.sourceforge.net/svnroot/tikiwiki"
WORKDIR="/home/$USER/tikipack"
MODULE="tikiwiki"

# end of configuration
# ############################################################

if [ -z $2 ]; then
echo "Usage: tikirelease.sh <release-version> <svn-relative-path>"
	echo "  <release-version> in separated by dots like in 2.0.RC1"
	echo "  <svn-relative-path> as in subversion (ex: branches/2.0 , tags/2.0)"
	exit 0
fi

OLDIR=`pwd`
VER=$1
RELTAG=$2

# ############################################################

mkdir -p $WORKDIR
cd $WORKDIR

if [ -d $VER ]; then
	rm -rf $VER
fi
mkdir $VER
cd $VER
svn export $SVNROOT/$RELTAG $MODULE-$VER
find $MODULE-$VER -name .cvsignore -type f -exec rm -f {} \;
find $MODULE-$VER -name .svnignore -type f -exec rm -f {} \;
find $MODULE-$VER -type d -exec chmod 775 {} \;
find $MODULE-$VER -type f -exec chmod 664 {} \;

# some more cleanup
rm -rf $MODULE-$VER/tests
rm -rf $MODULE-$VER/db/convertscripts
rm -rf $MODULE-$VER/doc/devtools

tar -czf $MODULE-$VER.tar.gz $MODULE-$VER
tar -cjf $MODULE-$VER.tar.bz2 $MODULE-$VER
zip -r $MODULE-$VER.zip $MODULE-$VER

echo ""
echo "To upload the 'tarballs', copy-paste and execute the following line (and change '\$SF_LOGIN' by your SF.net login):"
echo "cd $WORKDIR/$VER; scp $MODULE-$VER.tar.gz $MODULE-$VER.tar.bz2 $MODULE-$VER.zip \$SF_LOGIN@frs.sourceforge.net:uploads"
echo ""

cd $OLDIR

echo "Done."
