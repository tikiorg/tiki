#!/bin/sh
# $Id: tikirelease.sh,v 1.2 2003-08-21 00:51:21 redflo Exp $
# TODO
# - put release number as variable
# - add a chown / chmod before tarball

VER="1.7"
RELTAG="REL-1-7"
#RELTAG="release_eta_carinea_rc1"

CVSROOT=":ext:mose@cvs.sf.net:/cvsroot/tikiwiki"
WORKDIR="/home/mose/tikipack"
OLDIR=`pwd`

cd $WORKDIR
mkdir $VER
cd $VER
cvs -z3 -q -d $CVSROOT co -d tikiwiki_$VER -r $RELTAG tiki
find tikiwiki_$VER -name CVS -type d | xargs -- rm -rf
find tikiwiki_$VER -name .cvsignore -type f -exec rm -f {} \;
find tikiwiki_$VER -name Thumbs.db -exec rm -f {} \;
rm -rf tikilight_$VER/tests

tar -czf tikiwiki_$VER.tar.gz tikiwiki_$VER
tar --bzip2 -cf tikiwiki_$VER.tar.bz2 tikiwiki_$VER
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
echo "lftp -u anonymous,tiki@mose.com -e 'cd incoming;put tikiwiki_$VER.tar.gz;put tikiwiki_$VER.tar.bz2;quit;' upload.sf.net"

cd $OLDIR

echo "Done."
