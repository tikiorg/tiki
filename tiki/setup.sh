#!/bin/sh
# $Header: /cvsroot/tikiwiki/tiki/setup.sh,v 1.26 2004-05-04 22:20:21 mose Exp $

# Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
# All Rights Reserved. See copyright.txt for details and a complete list of authors.
# Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

DIRS="backups db dump img/wiki img/wiki_up modules/cache temp temp/cache templates_c templates styles"

if [ -d 'lib/Galaxia' ]; then
	DIRS=$DIRS" lib/Galaxia/processes"
fi

echo $DIRS

AUSER=nobody
AGROUP=nobody
RIGHTS=02775
VIRTUALS=""

UNAME=`uname | cut -c 1-6`

if [ -f /etc/debian_version ]; then
	AUSER=www-data
	AGROUP=www-data
fi

if [ -f /etc/redhat-release ]; then
	AUSER=apache
	AGROUP=apache
fi

if [ "$UNAME" = "CYGWIN" ]; then
	AUSER=SYSTEM
	AGROUP=SYSTEM
fi

if [ -z "$1" ]; then
	cat <<EOF
This script assigns necessary permissions for the directories that the
webserver writes files to. It also creates the (initially empty) cache 
directories.

Usage $0 user [group] [rights] [list of virtual host domains]

For example, if apache is running as user $AUSER and group $AGROUP, type:

  su -c '$0 $AUSER'
 
Alternatively, you may wish to set both the user and group:
  
  su -c '$0 $USER $AGROUP'

This will allow you to delete certain files/directories without becoming root.
  
Or, if you can't become root, but are a member of the group apache runs under
(for example: $AGROUP), you can type:

  $0 $USER $AGROUP

If you can't become root, and are not a member of the apache group, then type:

  $0 $USER yourgroup 02777

Replace yourgroup with your default group.

NOTE: If you do execute this last command, you will not be able to delete 
certain files created by apache, and will need to ask your system
administrator to delete them for you if needed.

To use Tiki's multi-site capability (virtual hosts from a single DocumentRoot)
add a list of domains to the command to create all the needed directories.
For example:

  su -c '$0 $USER $AGROUP $RIGHTS domain1 domain2 domain3'

or, if you can't become root:

  $0 $USER $AGROUP 02777 domain1 domain2 domain3

EOF
	exit 1
fi

if [ -n "$1" ]; then
	AUSER=$1
	shift
fi
if [ -n "$1" ]; then
	AGROUP=$1
	shift
fi
if [ -n "$1" ]; then
	RIGHTS=$1
	shift
fi

if [ -n "$1" ]; then
	VIRTUALS=$@
fi

# Create directories as needed
for dir in $DIRS
do
	if [ ! -d $dir ]; then
		echo Creating directory "$dir"
		mkdir -p $dir
	fi
        for vdir in $VIRTUALS; do
                if [ ! -d "$dir/$vdir" ]; then
			echo Creating directory "$dir/$vdir"
                        mkdir -p "$dir/$vdir"
                fi
        done
done

# Set ownerships of the directories
chown -R $AUSER $DIRS

if [ -n "$AGROUP" ]; then
	chgrp -R $AGROUP $DIRS
        chgrp $AGROUP robots.txt
fi

chmod -R $RIGHTS $DIRS

chown $AUSER robots.txt
chmod $RIGHTS robots.txt

# by setting the rights to tiki-install.php tiki-installer can be used in most cases to rename the file.
chown $AUSER tiki-install.php
chmod $RIGHTS tiki-install.php

exit 0

