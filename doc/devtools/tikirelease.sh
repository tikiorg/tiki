#!/bin/sh
# $Id$
# written and maintained by mose@tikiwiki.org
#
# HOWTO release Tikiwiki ?
# --------------------------
#
# 
# pre/
#    - update changelog.txt (from SVN commit logs)
#    - update copyright.txt (we _need_ a way to automate this - it was omitted for 1.9.2 release)
#    - update README
#    - run doc/devtools/securitycheck.php and check each "potentially unsafe" file.
#    - run doc/devtools/diffsql.sh to make sure tiki.sql and upgrade script from 
#        previous version give the same db structure (but not necessarily the same data).
#        The upgrade script is designed to be ran again & again. Specifically, we don't 
#        want new permissions or modules to appear at upgrade. If there is a chance that 
#        someone chose to delete something, it should not re-appear at each upgrade. 
#    - cd db/convertscripts and run convertsqls.sh
#    - update list of valid releases in tiki-admin_security.php:
#        array(1=>'1.9.1',2=>'1.9.1.1',3=>'1.9.2',4=>'1.9.3.1', etc etc etc);
#    - increment version number ($TWV->version) in lib/setup/twversion.class.php
#    - check for PHP syntax errors: find . -type f -name \*.php -exec php -l {} \;  | grep Parse
#    - commit your changes
#    - create the checksum file: copy doc/devtools/tiki-create_md5.php in tiki root 
#        and load that page in your browser
#
# 1/ Create and test pre-release packages by executing the script with the release
#    version as argument, using the format major.minor.sub 
#    bash tikirelease.sh 3.0.preRC3 branches/3.0
#
# 2/ Test the produced tarball and share the testing : you need at least 3 install 
#    from 3 different people
# 
# 3/ After testing, tag the release
#    ex: svn copy https://tikiwiki.svn.sourceforge.net/svnroot/tikiwiki/branches/3.0 https://tikiwiki.svn.sourceforge.net/svnroot/tikiwiki/tags/3.0.RC3
#    
# 4/ Build the release tarballs
#    bash tikirelease.sh 3.0.RC3 tags/3.0.RC3
#    
# 5/ Test the produced tarball and share the testing : you need at least 3 install 
#    from 3 different people
# 
# 6/ When the tarball is tested once you can copy-paste the produced line to upload
#    both .gz and .bz2 to sourceforge
#    
# 7/ If you are release technician on sourceforge, add the files to the repository 
#    in admin sf section. If you are not, ask a release technician to do it 
# 
# 8/ Warn people that do .rpm and ebuilds that the archive is avalaible so they can
#    complete the packaging process with new files. If you don't know who does that,
#    warn everybody.
#
# 9/ unless in step 8/ you warned everybody you have now to announce the good news
#    on devel mailing-list and ask marc to launch the announce-speading process 
#    (Freshmeat, SourceForge and tikiwiki.org (manually for now).
#
# post/ After release, update templates/tiki-install.tpl and 
#       templates/tiki-top_bar.tpl (including templates/styles/*/tiki-top_bar.tpl) 
#       to next version number with CVS   ex.: 1.9.2 (CVS)  . This helps later on to 
#       know exactly which files were included or not in a release.
#
#		- Also, update appropriate tw.o/*.version file with new release version
#
#
# All that process has to be relayed on live irc channel : 
# irc://irc.freenode.net/#tikiwiki
#
# ############################################################
# start of configuration
# change here what you need to fit your environment

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
find $MODULE-$VER -name CVS -type d | xargs -- rm -rf
find $MODULE-$VER -name .svn -type d | xargs -- rm -rf
find $MODULE-$VER -name .cvsignore -type f -exec rm -f {} \;
find $MODULE-$VER -name .svnignore -type f -exec rm -f {} \;
find $MODULE-$VER -name Thumbs.db -exec rm -f {} \;
find $MODULE-$VER -type d -exec chmod 775 {} \;
find $MODULE-$VER -type f -exec chmod 664 {} \;
# some more cleanup
rm -rf $MODULE-$VER/tests
rm -rf $MODULE-$VER/db/convertscripts
rm -rf $MODULE-$VER/db/convert_nulls_to_non_nulls.*
rm -rf $MODULE-$VER/doc/devtools
rm -rf $MODULE-$VER/bin
rm -rf $MODULE-$VER/CVSROOT
rm -rf $MODULE-$VER/SPIDERCORE
rm -rf $MODULE-$VER/Smarty
rm -rf $MODULE-$VER/templates_c/%*

tar -czf $MODULE-$VER.tar.gz $MODULE-$VER
tar -cjf $MODULE-$VER.tar.bz2 $MODULE-$VER
zip -r $MODULE-$VER.zip $MODULE-$VER

echo ""
echo "To upload the archives, copy-paste and exectue the following line at will (depending on SF's mood):"
echo "cd $WORKDIR/$VER; lftp -u anonymous,release@tikiwiki.org -e 'cd incoming;put $MODULE-$VER.tar.gz;put $MODULE-$VER.tar.bz2;put $MODULE-$VER.zip;quit;' upload.sf.net"
echo ""

cd $OLDIR

echo "Done."
