#!/bin/sh
# (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
# 
# All Rights Reserved. See copyright.txt for details and a complete list of authors.
# Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
# $Id$

# work in progress
# 
# **** DO NOT RUN ****
# 
# unless you can read the source, and understand what it does
# mose at tw.o

TAG1=BRANCH-1-9-HEAD
TAG2=BRANCH-1-9
RCSok=0
DIFFok=0

for i in `cvs -q diff --brief -r $TAG1 -r $TAG2 $1 | grep "\(RCS file:\|diff -u \)" | sed -e "s/ /:/g"`; do
	if [ ".$RCSok" = ".0" ]; then
		RCSfile=`echo $i | sed -e "s/RCS:file::\(.*\),v/\1/" -e "s~/cvsroot/tikiwiki/tiki/~~" -e "s~Attic/~~"`
		RCSok=1
	else
		DIFFcmd=`echo $i | sed -e "s/-u:--brief://" -e "s/:/ /g"`
		LOGcmd=`echo $DIFFcmd | sed -e "s/diff /log -N /" -e "s/ -r\([0-9\.]*\) -r\([0-9\.]*\)/ -r\1::\2/"`
		DIFFok=1
	fi
	if [ "${DIFFok}.${RCSok}" = "1.1" ]; then
		echo $RCSfile
		echo "cvs $DIFFcmd $RCSfile"
		echo "-------"
		cvs $LOGcmd $RCSfile | sed -e "1,10d" | grep -v "revision " | grep -v "date: " | grep -v -- '-----------'
		RCSok=0
		DIFFok=0
	fi
done
