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
# 0/ Setup the lines in the configuration section just below with your own
#    identity and settings (note that the script could be used on other projects)
#
# 1/ Create and test pre-release packages by executing the script with the release
#    version as argument, using the format major.minor.sub 
#    ./tikirelease.sh 1.9.preRC3
#
# 2/ Test the produced tarball and share the testing : you need at least 3 install 
#    from 3 different people
# 
# 3/ After testing, tag the release with instructions on http://tikiwiki.org/TikiCvsTags
#    cvs -d:ext:mose@tikiwiki.cvs.sf.net:/cvsroot/tikiwiki rtag -r BRANCH-1-9 REL-1-9-RC3 fulltiki
#    
# 4/ Uncomment the second "RELTAG=" line and the "grep -rl" line as instructed below
#
# 5/ Execute the script with the release version as argument, using the format
#    major.minor.sub (like in 1.9.RC3)
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

CVSROOT=":ext:$USER@tikiwiki.cvs.sf.net:/cvsroot/tikiwiki"
WORKDIR="/home/$USER/tikipack"
MODULE="tikiwiki"

# when creating pre-release packages, change RELTAG to the correct branch (ex:BRANCH-1-9)
# comment this line when ready to release (step 3)
RELTAG="BRANCH-1-9"

# end of configuration
# ############################################################

if [ -z $1 ]; then
echo "Usage: tikirelease.sh <release-tag>"
	echo "  separated by dots like in 1.9.RC3"
	exit 0
fi

OLDIR=`pwd`
VER=$1

# when ready to release (step 3), uncomment this line
# RELTAG="REL-`echo $VER | tr '.' '-'`"

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
rm -rf $MODULE-$VER/CVSROOT
rm -rf $MODULE-$VER/SPIDERCORE
rm -rf $MODULE-$VER/Smarty
rm -rf $MODULE-$VER/templates_c/%*

# uncomment for real release: remove all instances of "(CVS)" in templates
# grep -rl ' (CVS)' $MODULE-$VER/templates | xargs -- perl -pi -e "s/ \(CVS\)//"
# or that one for the pre-release test tarball
# grep -rl ' (CVS)' $MODULE-$VER/templates | xargs -- perl -pi -e "s/ \(CVS\)/ (pre-release)/"
chmod 775 $MODULE-$VER/setup.sh
cd $MODULE-$VER && ./setup.sh && cd ..

tar -czf $MODULE-$VER.tar.gz $MODULE-$VER
tar -cjf $MODULE-$VER.tar.bz2 $MODULE-$VER
zip -r $MODULE-$VER.zip $MODULE-$VER

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
# echo ""
# echo "To upload the light archives, copy-paste and exectue the following line at will (depending on SF's mood):"
# echo "cd $WORKDIR/$VER; lftp -u anonymous,release@tikiwiki.org -e 'cd incoming;put $MODULE-$VER.light.tar.gz;put $MODULE-$VER.light.tar.bz2;put $MODULE-$VER.light.zip;quit;' upload.sf.net"
# ############################################################

echo ""
echo "To upload the archives, copy-paste and exectue the following line at will (depending on SF's mood):"
echo "cd $WORKDIR/$VER; lftp -u anonymous,release@tikiwiki.org -e 'cd incoming;put $MODULE-$VER.tar.gz;put $MODULE-$VER.tar.bz2;put $MODULE-$VER.zip;quit;' upload.sf.net"
echo ""

cd $OLDIR

echo "Done."
