#!/bin/sh
# $Id: tikirelease.sh,v 1.6 2004-05-01 01:06:21 damosoft Exp $
# written and maintained by mose@feu.org

# HOWTO release TikiWiki ?
# --------------------------
# 
# 0/ Check that everything is working. When you are sure, check again.
# 
# 1/ Tag the release with instructions on http://tikiwiki.org/TikiCvsTags
#    for example : cvs tag  REL-1-7-3
#    
# 2/ Setup the lines in the configuration section just below with your own
#    identity and settings (note that the script could be used on other projects)
#    
# 3/ Execute the script with the release version as argument, under the shape
#    major.minor.sub (like in 1.7.3)
#    
# 4/ Test the produced tarball and share the testing with friends if possible
# 
# 5/ When the tarball is validated you can copyu-paste the produced line to upload
#    both .gz and .bz2 to sourceforge
#    
# 6/ If you are release technician on sourceforge, add the files to the repository 
#    in admin sf section. If you are not, ask one release technician to do it 
# 
# 7/ Warn people that do .zip, .7z, .rpm that the archive is avalaible so they can
#    complete the packaging process with new files. If you don't know who does that,
#    warn everybody.
#
# 8/ unless in step 7/ you warned everybody you have now to announce the good news
#    on devel mailing-list and ask marc to launch the announce-speading process 
#    (manually for now).
#
#
# All that process has to be relayed on live irc channel : irc.freenode.net #tikiwiki
#
#
# ############################################################
# start of configuration
# change here what you need to fit your environment

CVSROOT=":ext:mose@cvs.sf.net:/cvsroot/tikiwiki"
WORKDIR="/home/mose/tikipack"
MODULE="tikiwiki"

# end of configuration
# ############################################################

if [ -z $1 ]; then
echo "Usage: tikirelease.sh <release-tag>"
	echo "  separated by dots like in 1.7.3"
	exit 0
fi

OLDIR=`pwd`
VER=$1
RELTAG="REL-`echo $VER | tr '.' '-'`"

# ############################################################

cd $WORKDIR

if [ -d $VER ]; then
	rm -rf $VER
fi
mkdir $VER
cd $VER
cvs -z3 -q -d $CVSROOT co -d $MODULE-$VER -r $RELTAG $MODULE
find $MODULE-$VER -name CVS -type d | xargs -- rm -rf
find $MODULE-$VER -name .cvsignore -type f -exec rm -f {} \;
find $MODULE-$VER -name Thumbs.db -exec rm -f {} \;
find $MODULE-$VER -type d -exec chmod 775 {} \;
find $MODULE-$VER -type f -exec chmod 664 {} \;
# some more cleanup
rm -rf $MODULE-$VER/tests
rm -rf $MODULE-$VER/db/convertscripts
rm -rf $MODULE-$VER/db/convert_nulls_to_non_nulls.*
rm -rf $MODULE-$VER/doc/devtools
rm -rf $MODULE-$VER/bin

#uncomment for real release
#grep -rl ' (CVS) ' templates | xargs -- perl -pi -e "s/ \(CVS\) / /"
chmod 775 $MODULE-$VER/setup.sh

tar -czf $MODULE-$VER.tar.gz $MODULE-$VER
tar -cjf $MODULE-$VER.tar.bz2 $MODULE-$VER
zip -r $MODULE-$VER.zip $MODULE-$VER

echo ""
echo "copy-paste and exectue the following line at will (depending on SF mood) :"
echo "  lftp -u anonymous,release@tikiwiki.org -e 'cd incoming;put $MODULE-$VER.tar.gz;put $MODULE-$VER.tar.bz2;put $MODULE-$VER.zip;quit;' upload.sf.net"
echo ""

# ############################################################
# special operation for a lighter tikiwiki
# 
# cp -rp $MODULE-$VER tikilight_$VER
# find tikilight_$VER/img/avatars/ -type f -name '*.gif' | grep -v 000 | xargs -- rm -f
# rm -rf tikilight_$VER/img/custom
# find tikilight_$VER/lang -type f -name language.php | grep -v "/en/" | xargs -- rm -rf
# rm -rf tikilight_$VER/lib/Galaxia/docs
# rm -rf tikilight_$VER/calendar/iCal
# rm -rf tikilight_$VER/lib/pear/SOAP/example
# find tikilight_$VER/lib/pdflib/fonts -type f -name "*.afm" | grep -v php_Helvetica | grep -v php_Courier | xargs -- rm -f
# find tikilight_$VER/templates/styles/* -type d | grep -v elegant | grep -v moreneat | xargs -- rm -rf
# find tikilight_$VER/styles/* -type d | grep -v elegant | grep -v moreneat | xargs -- rm -rf
# find tikilight_$VER/styles/ -type f -name "*.css" | grep -v elegant | grep -v moreneat | xargs -- rm -f
# tar -czf $MODULE-$VER.light.tar.gz tikilight_$VER
# tar -cjf $MODULE-$VER.light.tar.bz2 tikilight_$VER
# zip -r $MODULE-$VER.light.zip tikilight_$VER
# echo "lftp -u anonymous,tiki@mose.com -e 'cd incoming;put $MODULE-$VER.light.tar.gz;put $MODULE-$VER.light.tar.bz2;put $MODULE-$VER.light.zip;quit;' upload.sf.net"
# ############################################################

cd $OLDIR

echo "Done."
