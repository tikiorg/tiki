#!/bin/sh

# $CVSHeader$

# Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
# All Rights Reserved. See copyright.txt for details and a complete list of authors.
# Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

DIRS="backups db dump img/wiki img/wiki_up modules/cache temp templates_c var var/log var/log/ircbot"
USER=yourlogin
GROUP=nobody

UNAME=`uname | cut -c 1-6`

if [ "$UNAME" = "CYGWIN" ];
then
	USER=SYSTEM
	GROUP=SYSTEM
fi

if [ -z "$1" ];
then
	cat <<EOF
This script assigns necessary permissions for the directories that the webserver writes files to.
It also creates the (initially empty) cache directories.

Usage $0 user [group]

For example, if apache is running as user $USER, type:

  su -c '$0 $GROUP'
 
Alternatively, you may wish to set both the user and group:
  
  su -c '$0 $USER $GROUP'

This will allow you to delete certain files/directories without becoming root.
  
Or, if you can't become root, but are a member of the group apache runs under
(for example: $GROUP), you can type:

  $0 $USER $GROUP
  
EOF
exit 1
fi

for dir in $DIRS
do
	if [ ! -d $dir ]
	then
		mkdir -p $dir
	fi
done

chown -R $1 $DIRS

if [ -n "$2" ];
then
	chgrp -R $2 $DIRS
fi

chmod -R 02775 $DIRS
