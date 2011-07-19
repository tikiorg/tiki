#!/bin/bash
# (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
# 
# All Rights Reserved. See copyright.txt for details and a complete list of authors.
# Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
# $Id$
#
# A script to dump a TW and SCP copy it somewhere
# Make sure you change the default values for your setup!
#
# based on a script by mose hacked up by Damo :)

# The path to tikiroot
TIKIDIR="/home/tikiwiki/html"
# Multitiki Domain to work with
VIRTUAL=""
# A path to store the temp backup
ARCHIVEDIR="/tmp"
# Somewhere to copy to
SCPTO="username@server.tld:path/to/backup"

eval `cat $TIKIDIR/db/$VIRTUAL/local.php | sed -e '/[\?#]/d' -e "s/\$\([-_a-z]*\)[[:space:]]*=[[:space:]]*\([-_a-zA-Z0-9\"'\.]*\);/\\1=\\2/"`
DBHOST=${host_tiki:-'localhost'}
DBNAME=${dbs_tiki:-'tikiwiki'}
DBUSER=${user_tiki:-'root'}
DBPASS=${pass_tiki:-''}

NOWDATE=`date +%Y-%m-%d`
DUMPFILE="$DBNAME-$NOWDATE-sql.bz2"

cd $ARCHIVEDIR

mysqldump -Q -u $DBUSER -h$DBHOST -p$DBPASS $DBNAME | bzip2 -c > $DUMPFILE
if [ "$SCPTO" ]; then 
	scp -C $DUMPFILE $SCPTO
	rm $DUMPFILE
fi
