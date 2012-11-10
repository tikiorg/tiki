#! /bin/sh

# (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
# 
# All Rights Reserved. See copyright.txt for details and a complete list of authors.
# Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
# $Id$

# This file sets permissions and creates relevant folders for Tiki.
#

# part -1 - developer comment
# ---------------------------
#
# This version is supposed to act exactly the same way as setup.sh revision 43875
# if this script runs in production mode. Minor changes are:
# - debugging mode for further improvements
#   and adaption of permission check data
# - PATH
# - it should be possible to run this script as executable
# - order of commands changed
# - command blocks are encapsulated in functions
#
# further plan for smooth transition: at some time move actual
# setup.sh to setup-legacy.sh and this setup-revamp.sh to setup.sh
# and later remove setup-legacy.sh

# part 0 - choose production mode or verbose debugging mode
# ---------------------------------------------------------

#DEBUG=0 # production mode
DEBUG=1 # debugging mode

# part 1 - preliminaries
# ----------------------

#PATH="${PATH}:/bin:/usr/bin:/sbin:/usr/sbin:/usr/local/bin:/usr/local/sbin:/opt/bin:/opt/sbin:/opt/local/bin:/opt/local/sbin"
for ADDPATH in `echo /bin /usr/bin /sbin /usr/sbin /usr/local/bin /usr/local/sbin /opt/bin /opt/sbin /opt/local/bin /opt/local/sbin` ; do
	if [ -d ${ADDPATH} ] ; then
		PATH="${PATH}:${ADDPATH}"
		if [ ${DEBUG} = '1' ] ; then
			 echo ${ADDPATH} exists
		fi
	else
		if [ ${DEBUG} = '1' ] ; then
			echo ${ADDPATH} does not exist
		fi
	fi
done

# hint for users
usage() {
	cat <<EOF
usage: $0 [<switches>] open|fix
-h           show help
-u user      owner of files (default: $AUSER)
-g group     group of files (default: $AGROUP)
-v virtuals  list of virtuals (for multitiki, example: "www1 www2")
-n           not interactive mode
EOF
}

if [ ${DEBUG} = '1' ] ; then
	echo usage output: begin
	usage
	echo usage output: end
fi

OPT_AUSER=
OPT_AGROUP=
OPT_VIRTUALS=
OPT_NOTINTERACTIVE=

while getopts "hu:g:v:n" OPTION; do
	if [ ${DEBUG} = '1' ] ; then
		echo option: ${OPTION}
	fi
	case $OPTION in
		h) usage ; exit 0 ;;
		u) OPT_AUSER=$OPTARG ;;
		g) OPT_AGROUP=$OPTARG ;;
		v) OPT_VIRTUALS=$OPTARG ;;
		n) OPT_NOTINTERACTIVE=1 ;;
		?) usage ; exit 1 ;;
	esac
done
shift $(($OPTIND - 1))

# define command to execute for main program
if [ -z $1 ]; then
	COMMAND=fix
else
	COMMAND=$1
fi

if [ ${DEBUG} = '1' ] ; then
	echo COMMAND: ${COMMAND}
fi

# part 2 - distribution check
# ---------------------------

AUSER=nobody
AGROUP=nobody
VIRTUALS=""
USER=`whoami`

check_distribution() {
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
	elif [ "$UNAME" = "Darwin" ]; then
		AUSER=_www
		AGROUP=_www
	fi
fi
}

check_distribution

# part 3 - writable subdirs
# -------------------------

DIRS="db dump img/wiki img/wiki_up img/trackers modules/cache temp temp/cache temp/public templates_c templates styles maps whelp mods files tiki_tests/tests temp/unified-index"

# part 4 - several functions
# --------------------------

# part 4.1 - several permission settings for different usecases

permission_default() {
	chmod -fR u=rwX,go=rX .
}

permission_exceptions() {
	chmod o-rwx db/local.php
}

# part 4.2 - several command options as fix, open, ...

command_fix() {
	if [ "$USER" = 'root' ]; then
		if [ -n "$OPT_AUSER" ]; then
			AUSER=$OPT_AUSER
		elif [ -z "$OPT_NOTINTERACTIVE" ]; then
			read -p "User [$AUSER]: " REPLY
			if [ -n "$REPLY" ]; then
				AUSER=$REPLY
			fi
		fi
	else
		if [ -z "$OPT_NOTINTERACTIVE" ]; then
			echo "You are not root or you are on a shared hosting account. You can now:

1- ctrl-c to break now.

or

2- If you press enter to continue, you will probably get some error messages
but it (the script) will still fix what it can according to the permissions
of your user. This script will now ask you some questions. If you don't know
what to answer, just press enter to each question (to use default value)"

			read -p "> Press enter to continue: " WAIT
			AUSER=$USER
		fi
	fi

	if [ -n "$OPT_AGROUP" ]; then
		AGROUP=$OPT_AGROUP
	elif [ -z "$OPT_NOTINTERACTIVE" ]; then
		read -p "> Group [$AGROUP]: " REPLY
		if [ -n "$REPLY" ]; then
			AGROUP=$REPLY
		fi
	fi

	touch db/virtuals.inc
	if [ -n "$OPT_VIRTUALS" ]; then
		VIRTUALS=$OPT_VIRTUALS
	elif [ -n "$OPT_NOTINTERACTIVE" ]; then
		VIRTUALS=$(cat db/virtuals.inc)
	else
		read -p "> Multi [$(cat -s db/virtuals.inc | tr '\n' ' ')]: " VIRTUALS
		[ -z "$VIRTUALS" ] && VIRTUALS=$(cat db/virtuals.inc)
	fi

	if [ -n "$VIRTUALS" ]; then
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
		if [ -n "$VIRTUALS" ] && [ $dir != "temp/unified-index" ]; then
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

	# Check that the USER is in AGROUP
	USERINAGROUP="no"
	for grp in `id -Gn $USER`; do
		if [ "$grp" = "$AGROUP" ]; then
			USERINAGROUP="yes"
		fi
	done

	echo "Fix global perms ..."
	if [ "$USER" = 'root' ]; then
		#chown -fR $AUSER:$AGROUP . || echo "Could not change ownership to $AUSER"
		echo -n "Change user to $AUSER and group to $AGROUP..."
		chown -fR $AUSER:$AGROUP .
		echo " done."
	else
		if [ -n "$OPT_AUSER" ]; then
			echo "You are not root. We will not try to change the file owners."
		fi
		if [ "$USERINAGROUP" = "yes" ]; then
			echo -n "Change group to $AGROUP ..."
			chgrp -Rf $AGROUP .
			echo " done."
		else
			echo "You are not root and you are not in the group $AGROUP. We can't change the group ownership to $AGROUP."
			echo "Special dirs permissions will be set accordingly."
		fi
	fi

#	find . ! -regex '.*^\(devtools\).*' -type f -exec chmod 644 {} \;	
#	echo -n " files perms fixed ..."
#	find . -type d -exec chmod 755 {} \;
#	echo " dirs perms fixed ... done"

	echo -n "Fix normal dirs ..."
	chmod -fR u=rwX,go=rX .
	echo " done."

	echo -n "Fix special dirs ..."
	if [ "$USER" = 'root' -o "$USERINAGROUP" = "yes" ]; then
		chmod -R g+w $DIRS
	else
		chmod -fR go+w $DIRS
	fi

#	chmod 664 robots.txt tiki-install.php

	echo " done."
}

command_open() {
	if [ "$USER" = 'root' ]; then
		if [ -n "$OPT_AUSER" ]; then
			AUSER=$OPT_AUSER
		elif [ -z "$OPT_NOTINTERACTIVE" ]; then
			read -p "User [$AUSER]: " REPLY
			if [ -n "$REPLY" ]; then
				AUSER=$REPLY
			fi
		fi
		chown -R $AUSER .
	else
		echo "You are not root or you are on a shared hosting account. We will not try to change the file owners."
	fi

	chmod -R a=rwX .

	echo " done"
}

# debug exit
if [ ${DEBUG} = '1' ] ; then
	echo Exiting... for production mode set DEBUG=0 at the beginning of this script
	exit 1
fi

# part 5 - main program
# ---------------------

if [ "$COMMAND" = 'fix' ]; then
	command_fix
elif [ "$COMMAND" = 'open' ]; then
	command_open
else
	echo "Type 'fix' or 'open' as command argument."
fi

exit 0

# EOF
