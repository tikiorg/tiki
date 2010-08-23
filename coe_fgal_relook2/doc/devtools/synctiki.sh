#!/bin/bash
# (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
# 
# All Rights Reserved. See copyright.txt for details and a complete list of authors.
# Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
# $Id$

# that script runs intelligent syncronize operation.
# It reads db/local.php to find proper mysql info
# and duplicate db on a local mirror
# 
# tested on debian only for now
# 
# mose@tikiwiki.org

# Remote informations
RHOST="tikiwiki.org"
RTIKI="/usr/local/tiki18"
RTMPDIR="/tmp"

eval `ssh $RHOST cat $RTIKI/db/local.php | sed -e '/[\?#]/d' -e "s/\$\([-_a-z]*\)[[:space:]]*=[[:space:]]*\([-_a-zA-Z0-9\"'\.]*\);/\\1=\\2/"`
RDBHOST=${host_tiki:-'localhost'}
RDBNAME=${dbs_tiki:-'tikiwiki'}
RDBUSER=${user_tiki:-'root'}
RDBPASS=${pass_tiki:-''}


# Local informations
LTIKI="/var/tiki18"
LTMPDIR="/tmp"
LARCHDIR="~/tiki"

eval `sed -e '/[\?#]/d' -e "s/\$\([-_a-z]*\)[[:space:]]*=[[:space:]]*\([-_a-zA-Z0-9\"'\.]*\);/\\1=\\2/" $LTIKI/db/local.php`
LDBHOST=${host_tiki:-'localhost'}
LDBNAME=${dbs_tiki:-'tikiwiki'}
LDBUSER=${user_tiki:-'root'}
LDBPASS=${pass_tiki:-''}

# misc
DUMP="$RDBNAME.$RHOST.`date +%s`.sql"

# remote operations
  ssh $RHOST "mysqldump -e -f -h$RDBHOST -u$RDBUSER -p$RDBPASS $RDBNAME > $RTMPDIR/$DUMP"
  scp -C $RHOST:$RTMPDIR/$DUMP $LTMPDIR && ssh $RHOST "rm -f $RTMPDIR/$DUMP"
# local operations
  mysql -h$LDBHOST -u$LDBUSER -p$LDBPASS -e "drop database $LDBNAME;create database $LDBNAME;"
  mysql -h$LDBHOST -u$LDBUSER -p$LDBPASS $LDBNAME < $LTMPDIR/$DUMP
# afterwise
  bzip2 $LTMPDIR/$DUMP
  mv $LTMPDIR/$DUMP.bz2 $LARCHDIR
# finished
  echo "Done."

exit 0
