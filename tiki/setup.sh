#!/bin/sh

# $CVSHeader$

# Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
# All Rights Reserved. See copyright.txt for details and a complete list of authors.
# Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

DIRS="backups db dump img/wiki img/wiki_up modules/cache temp templates_c var var/log var/log/irc"
AUSER=nobody
AGROUP=nobody
RIGHTS=02775
VIRTUALS=""

UNAME=`uname | cut -c 1-6`

if [ "$UNAME" = "CYGWIN" ];
then
	AUSER=SYSTEM
	AGROUP=SYSTEM
fi

if [ -z "$1" ];
then
	cat <<EOF
This script assigns necessary permissions for the directories that the
webserver writes files to. It also creates the (initially empty) cache 
directories.

Usage $0 user [group] [rights] [list of virtual host domains]

For example, if apache is running as user $AUSER, type:

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

NOTE: If you do this, you will not be able to delete certain files created by
apache, and will need to ask your system administrator to delete them for you
if needed.

To use Tiki's multi-site capability (virtual hosts from a single doc root) add a list of domains
to the command to create all the needed directories, for example:

  su -c '$0 $USER $GROUP 02777 domain1 domain2 domain3'

EOF
exit 1
fi

AUSER=$1;shift
AGROUP=$1;shift
RIGHTS=$1;shift
VIRTUALS=$@

# Create directories as needed
for dir in $DIRS
do
	if [ ! -d $dir ]
	then
		mkdir -p $dir
	fi
        for vdir in $VIRTUALS; do
                if [ ! -d "$dir/$vdir" ]; then
                        mkdir -p "$dir/$vdir"
                        echo "$dir/$vdir missing ... created."
                else
                        echo "$dir/$vdir ok"
                fi
        done
done

# Set ownerships of the directories
chown -R $AUSER $DIRS

if [ -n "$AGROUP" ];
then
	chgrp -R $AGROUP $DIRS
fi

if [ -z "$RIGHTS" ];
then
	RIGHTS=02775
fi

chmod -R $RIGHTS $DIRS
