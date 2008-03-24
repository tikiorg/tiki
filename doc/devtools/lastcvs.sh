#!/bin/sh
# $Id: lastcvs.sh,v 1.2 2003-08-01 10:30:46 redflo Exp $
# This file is used to build tarball from developers cvs
# written by mose@mose.fr
#
# to install it :
#   mv lastcvs.sh /usr/local/bin
#   chmod +x /usr/local/bin/lastcvs.sh
# and then, use crontab -e with you developer account and add :
#   02 2,10,18 * * * /usr/local/bin/lastcvs.sh release_eta_carinea_rc1
#   02 1,9,17 * * * /usr/local/bin/lastcvs.sh HEAD
# with any change to fit you need


OLDIR=`pwd`
USER=`whoami`
CVSMODULE="tiki"
CVSEXT="-d:ext:$USER@cvs.sf.net:/cvsroot/tikiwiki"

if [ -z $1 ]; then
	TAG="HEAD"
else
	TAG=$1
fi

LOGFILE="/var/log/tiki/cvslog_$TAG"
LOCAL="/usr/local/tiki"
LOCALREP="lastiki_$TAG"

if [ ! -f $LOGFILE ]; then
	touch $LOGFILE
fi

date +%s >> $LOGFILE
if [ ! -d $LOCALREP ]; then
	cd $LOCAL
	cvs -z3 -q $CVSEXT co -r $TAG -d $LOCALREP $CVSMODULE >> $LOGFILE
else
	cd $LOCAL/$LOCALREP
	cvs -q update -dP -r $1 >> $LOGFILE
	cd ../
fi

if [ -f lastiki_$TAG.tar.bz2_previous ]; then
	rm -f lastiki_$TAG.tar.bz2_previous
	mv lastiki_$TAG.tar.bz2 lastiki_$TAG.tar.bz2_previous
fi
tar --bzip2 -cf lastiki_$TAG.tar.bz2 lastiki_$TAG
# if you prefer a .tar.gz uncomment next line
#   tar -czf lastiki_$TAG.tar.gz lastiki_$TAG

# resulting tarball should be in 
# /usr/local/tiki/lastiki_$TAG.tar.bz2

cd $OLDIR

exit 0
