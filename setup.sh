# $Header: /cvsroot/tikiwiki/tiki/fixperms.sh,v 1.9.2.2 2008-02-07 21:59:14 lphuberdeau Exp $

# Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
# All Rights Reserved. See copyright.txt for details and a complete list of authors.
# Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

# This file is a replacement for setup.sh
# in test in 1.9 version

DIRS="backups db dump img/wiki img/wiki_up img/trackers modules/cache temp temp/cache templates_c templates styles maps whelp mods files tiki_tests/tests"

if [ -d 'lib/Galaxia' ]; then
	DIRS=$DIRS" lib/Galaxia/processes"
fi

AUSER=nobody
AGROUP=nobody
VIRTUALS=""
USER=`whoami`

if [ -f /etc/debian_version ]; then
	AUSER=www-data
	AGROUP=www-data
elif [ -f /etc/redhat-release ]; then
	AUSER=apache
	AGROUP=apache
elif [ -f /etc/gentoo-release ]; then
	AUSER=apache
	AGROUP=apache
else
	UNAME=`uname | cut -c 1-6`
	if [ "$UNAME" = "CYGWIN" ]; then
		AUSER=SYSTEM
		AGROUP=SYSTEM
	fi
fi

if [ -z $1 ]; then
	COMMAND=fix
else
	COMMAND=$1
fi

if [ "$COMMAND" = 'fix' ]; then
	if [ "$USER" = 'root' ]; then
		echo -n "User [$AUSER]: "
		read REPLY 
		if [ -n "$REPLY" ]; then
			AUSER=$REPLY
		fi
	else
		echo "You are not root or you are on a shared hosting account. You can now:

1- ctrl-c to break now.

or

2- If you press enter to continue, you will probably get some error messages but it (the script) will 
still fix what it can according to the permissions of your user. This script will now ask you some 
questions. If you don't know what to answer, just press enter to each question (to use default value)"
	
		read 
		AUSER=$USER
	fi

	echo -n "Group [$AGROUP]: "
	read REPLY
	if [ -n "$REPLY" ]; then
		AGROUP=$REPLY
	fi

	echo -n "Multi []: "
	read VIRTUALS

	if [ -n "$VIRTUALS" ]; then
		touch db/virtuals.inc
		for vdir in $VIRTUALS; do
			echo $vdir >> db/virtuals.inc
			cat db/virtuals.inc | sort | uniq > db/virtuals.inc_new
			rm -f db/virtuals.inc && mv db/virtuals.inc_new db/virtuals.inc
		done
	fi

	echo "Checking dirs : "
	for dir in $DIRS; do
		echo -n "  $dir ... "
		if [ ! -d $dir ]; then
			echo -n " Creating directory"
			mkdir -p $dir
		fi
		echo " ok."
		if [ -n $VIRTUALS ]; then
			for vdir in $VIRTUALS; do
				echo -n "  $dir/$vdir ... "
				if [ ! -d "$dir/$vdir" ]; then
					echo -n " Creating Directory"
					mkdir -p "$dir/$vdir"
				fi
				echo " ok."
			done
		fi
	done

	echo -n "Fix global perms ..."
	chown -R $AUSER:$AGROUP .
	echo -n " chowned ..."

#	find . ! -regex '.*^\(devtools\).*' -type f -exec chmod 644 {} \;	
#	echo -n " files perms fixed ..."
#	find . -type d -exec chmod 755 {} \;
#	echo " dirs perms fixed ... done"

	chmod -R a-x .
	chmod -R u=rwX .
	chmod -R go=rX .

	echo " done."

	echo -n "Fix special dirs ..."
	if [ "$USER" = 'root' ]; then
		chmod -R g+w $DIRS
	else
		chmod -R go+w $DIRS
	fi

#	chmod 664 robots.txt tiki-install.php
	echo " done."

elif [ "$COMMAND" = 'open' ]; then
	if [ "$USER" = 'root' ]; then
		echo -n "User [$AUSER]: "
		read REPLY
		if [ -n "$REPLY" ]; then
			AUSER=$REPLY
		fi		
		echo -n "Open global perms ..."
		chown -R $AUSER .
		echo " done"
	else
		echo "You are not root or you are on a shared hosting account. You can now:

1- ctrl-c to break now.

or

2- If you press enter to continue, you will probably get some error messages but it (the script) will 
still fix what it can according to the permissions of your user. This script will now ask you some 
questions. If you don't know what to answer, just press enter to each question (to use default value)"

		read 
		echo -n "Open global perms ..."
#		find . -type d -exec chmod 777 {} \;
#		find . -type f -exec chmod 666 {} \;
		chmod -R a=rwX .
		echo " done"
	fi
else
	echo "Type 'fix' or 'open' as command argument."
fi

exit 0

