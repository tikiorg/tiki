#!/bin/sh
# $Id: tikirelease.sh,v 1.3 2003-08-30 20:31:28 mose Exp $
# TODO
# - add a chown / chmod before tarball

if [ -z $1 ]; then
	echo "Usage: tikirelease.sh <release-tag>"
	echo "  separated by dots like in 1.7.1"
	exit 0
fi

# ############################################################
# start of configuration
# change here what you need to fit your environment

CVSROOT=":ext:mose@cvs.sf.net:/cvsroot/tikiwiki"
WORKDIR="/home/mose/tikipack"
MODULE="tikiwiki"
OLDIR=`pwd`

# end of configuration

VER=$1
RELTAG="REL-`echo $VER | tr '.' '-'`"

cd $WORKDIR
mkdir $VER
cd $VER
cvs -z3 -q -d $CVSROOT co -d $MODULE-$VER -r $RELTAG $MODULE
find "$MODULE-$VER" -name CVS -type d | xargs --  rm -rf
find "$MODULE-$VER" -name .cvsignore -type f -exec rm -f {} \;
find "$MODULE-$VER" -name Thumbs.db -exec rm -f {} \;
rm -rf $MODULE-$VER/tests

tar -czf $MODULE-$VER.tar.gz $MODULE-$VER
tar --bzip2 -cf $MODULE-$VER.tar.bz2 $MODULE-$VER
#cp -rp tikiwiki_$VER tikilight_$VER
#find tikilight_$VER/img/avatars/ -type f -name '*.gif' | grep -v 000 | xargs -- rm -f
#rm -rf tikilight_$VER/img/custom
#find tikilight_$VER/lang -type f -name language.php | grep -v "/en/" | xargs -- rm -rf
#rm -rf tikilight_$VER/lib/Galaxia/docs
#rm -rf tikilight_$VER/calendar/iCal
#rm -rf tikilight_$VER/lib/pear/SOAP/example
#find tikilight_$VER/lib/pdflib/fonts -type f -name "*.afm" | grep -v php_Helvetica | grep -v php_Courier | xargs -- rm -f
#find tikilight_$VER/templates/styles/* -type d | grep -v elegant | grep -v moreneat | xargs -- rm -rf
#find tikilight_$VER/styles/* -type d | grep -v elegant | grep -v moreneat | xargs -- rm -rf
#find tikilight_$VER/styles/ -type f -name "*.css" | grep -v elegant | grep -v moreneat | xargs -- rm -f
#tar -czf tikiwiki_$VER.light.tar.gz tikilight_$VER
#tar --bzip2 -cf tikiwiki_$VER.light.tar.bz2 tikilight_$VER

#echo "lftp -u anonymous,tiki@mose.com -e 'cd incoming;put tikiwiki_$VER.tar.gz;put tikiwiki_$VER.tar.bz2;put tikiwiki_$VER.light.tar.gz;put tikiwiki_$VER.light.tar.bz2;quit;' upload.sf.net"
echo "lftp -u anonymous,tiki@mose.com -e 'cd incoming;put $MODULE-$VER.tar.gz;put $MODULE-$VER.tar.bz2;quit;' upload.sf.net"

cd $OLDIR

echo "Done."
