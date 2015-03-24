#!/bin/sh -x
# (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
#
# All Rights Reserved. See copyright.txt for details and a complete list of authors.
# Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
# $Id$

#
# This script does an export of a given subversion tree and creates a set of
# release packages that can be uploaded to SourceForge and installed by end
# users.

# ==========================================================================
# IMPORTANT NOTE : This script must NOT be called directly !!!
#   It is used by the main release script (doc/devtools/release.php)
#   To get the Tiki release HOWTO, try: php doc/devtools/release.php --howto
# ==========================================================================
#

SVNROOT="http://svn.code.sf.net/p/tikiwiki/code"
WORKDIR="$HOME/tikipack"
MODULE="tiki"

# end of configuration
# ############################################################

if [ -z $2 ]; then
echo "Usage: tikirelease.sh <release-version> <svn-relative-path>"
	echo "  <release-version> in separated by dots like in 2.0.RC1"
	echo "  <svn-relative-path> as in subversion (ex: branches/2.0 , tags/2.0)"
	exit 0
fi

OLDIR="`pwd`"
VER=$1
RELTAG=$2

# ############################################################

if [ ! -d $WORKDIR ]; then
    mkdir -p $WORKDIR || die "Can't make $WORKDIR - $!"
fi

cd $WORKDIR || die "Can't get into $WORKDIR - $!"
echo "Working in $WORKDIR"

if [ -d $VER ]; then
    echo "Deleting old $VER"
    rm -rf $VER
fi
mkdir $VER
cd $VER

echo "Exporting $SVNROOT/$RELTAG $MODULE-$VER"
svn export $SVNROOT/$RELTAG $MODULE-$VER

if [ -f $MODULE-$VER/composer.json ]; then
	wget -N http://getcomposer.org/composer.phar
	cd $MODULE-$VER
	php ../composer.phar install --prefer-dist 2>&1 | sed '/Warning: Ambiguous class resolution/d'
	cd ..
fi

echo "Cleaning up"
find $MODULE-$VER -name .cvsignore -type f -exec rm -f {} \;
find $MODULE-$VER -name .svnignore -type f -exec rm -f {} \;
find $MODULE-$VER/lang/ -type f -name language.php -exec php $MODULE-$VER/doc/devtools/stripcomments.php  {} \;
php $MODULE-$VER/doc/devtools/rewritesecdb.php $VER

rm -rf $MODULE-$VER/tests
rm -rf $MODULE-$VER/db/convertscripts
rm -rf $MODULE-$VER/doc/devtools

echo "Setting permissions"
find $MODULE-$VER -type d -exec chmod 775 {} \;
find $MODULE-$VER -type f -exec chmod 664 {} \;

echo "Creating tarballs"
tar -czf $MODULE-$VER.tar.gz $MODULE-$VER
tar -cjf $MODULE-$VER.tar.bz2 $MODULE-$VER
zip -r -q $MODULE-$VER.zip $MODULE-$VER
7za a $MODULE-$VER.7z $MODULE-$VER

ls $WORKDIR/$VER

echo ""
echo "To upload the 'tarballs', copy-paste and execute the following line (and change '\$SF_LOGIN' by your SF.net login):"
echo "cd $WORKDIR/$VER; scp $MODULE-$VER.tar.gz $MODULE-$VER.tar.bz2 $MODULE-$VER.zip \$SF_LOGIN@frs.sourceforge.net:uploads"
echo ""

cd "$OLDIR"

echo "Done."
