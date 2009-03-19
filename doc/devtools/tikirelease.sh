#!/bin/sh
# $Id$
#
# ===========================================================
# IMPORTANT NOTE : This script should not be called manually.
#   It is called by doc/devtools/release.php
# ===========================================================
#
# HOWTO release Tikiwiki ?
# --------------------------
# 
# pre/
#    - run doc/devtools/release_changelog.php to update changelog.txt and check the diff with the last version
#    - run doc/devtools/release_copyright.php to update copyright.txt and check the diff with the last version
#    - update README
#    - Test the whole Installer and check if everything is OK
#    - run doc/devtools/securitycheck.php and check each "potentially unsafe" file.
#    - run doc/devtools/diffsql.sh to make sure tiki.sql and upgrade script from 
#        previous version give the same db structure (but not necessarily the same data).
#        The upgrade script is designed to be ran again & again. Specifically, we don't 
#        want new permissions or modules to appear at upgrade. If there is a chance that 
#        someone chose to delete something, it should not re-appear at each upgrade. 
#    - cd db/convertscripts and run convertsqls.sh
#    - in lib/setup/twversion.class.php
#      - increment the version number in the constructor
#      - update list of valid releases in getVersions()
#    - commit your changes
#
# 1/ Create and test pre-release packages by executing the script with the release
#    version as argument, using the format major.minor.sub 
#    php doc/devtools/release.php 2.0 preRC4
#
# 2/ Test the produced "tarballs" and share the testing : you need at least 3 installations
#    from 3 different people
# 
# 3/ After testing, tag the release, build the release "tarballs"
#    php doc/devtools/release.php 2.0 RC4
#    
# 4/ Test the produced "tarballs" and share the testing : you need at least 3 install 
#    from 3 different people
# 
# 5/ When the "tarballs" are tested, follow the steps to upload on SourceForge:
#    http://tinyurl.com/59uubv
#    
# 6/ Warn people that do .rpm and ebuilds that the archive is avalaible so they can
#    complete the packaging process with new files. If you don't know who does that,
#    warn everybody.
#
# 7/ unless in step 8/ you warned everybody you have now to announce the good news
#    on devel mailing-list and ask the TAG (TikiWiki Admin Group) through the admin
#    mailing-list to launch the announce-speading process 
#    (Freshmeat, SourceForge and tikiwiki.org (manually for now).
#
# post/ After release, update templates/tiki-install.tpl and 
#       templates/tiki-top_bar.tpl (including templates/styles/*/tiki-top_bar.tpl) 
#       to next version number with SVN   ex.: 1.9.2 (SVN)  . This helps later on to 
#       know exactly which files were included or not in a release.
#
#       - Also, update appropriate tw.o/*.version file with new release version
#       (or ask the TAG to do this)
#
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
