# (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
# 
# All Rights Reserved. See copyright.txt for details and a complete list of authors.
# Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
# $Id$

# This file is a replacement for setup.sh
# in test in 1.9 version

DIRS="backups db dump img/wiki img/wiki_up img/trackers modules/cache temp temp/cache temp/public templates_c templates styles maps whelp mods files tiki_tests/tests temp/unified-index"

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
	elif [ "$UNAME" = "Darwin" ]; then
		AUSER=_www
		AGROUP=_www
	fi
fi

usage() {
	cat <<EOF
usage: $0 [<switches>] open|fix
-h           show help
-u user      owner of files (default: $AUSER)
-g group     group of files (default: $AGROUP)
-v virtuals  list of virtuals (for multitiki, exemple: "www1 www2")
-n           not interactive mode
EOF
}

OPT_AUSER=
OPT_AGROUP=
OPT_VIRTUALS=
OPT_NOTINTERACTIVE=

while getopts "hu:g:v:n" OPTION; do
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

if [ -z $1 ]; then
	COMMAND=fix
else
	COMMAND=$1
fi

if [ "$COMMAND" = 'fix' ]; then
	if [ "$USER" = 'root' ]; then
		if [ -n "$OPT_AUSER" ]; then
			AUSER=$OPT_AUSER
		elif [ -z "$OPT_NOTINTERACTIVE" ]; then
			echo -n "User [$AUSER]: "
			read REPLY 
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
			
			read WAIT
			AUSER=$USER
		fi
	fi

	if [ -n "$OPT_AGROUP" ]; then
		AGROUP=$OPT_AGROUP
	elif [ -z "$OPT_NOTINTERACTIVE" ]; then
		echo -n "Group [$AGROUP]: "
		read REPLY
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
		echo -n "Multi ["$(cat db/virtuals.inc)"]: "
		read VIRTUALS
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
		if [ -n "$VIRTUALS" ]; then
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
	chown -fR $AUSER:$AGROUP .
	echo -n " chowned ..."

#	find . ! -regex '.*^\(devtools\).*' -type f -exec chmod 644 {} \;	
#	echo -n " files perms fixed ..."
#	find . -type d -exec chmod 755 {} \;
#	echo " dirs perms fixed ... done"

	chmod -fR u=rwX,go=rX .

	echo " done."

	echo -n "Fix special dirs ..."
	if [ "$USER" = 'root' ]; then
		chmod -R g+w $DIRS
	else
		chmod -fR go+w $DIRS
	fi

#	chmod 664 robots.txt tiki-install.php

	echo " done."

elif [ "$COMMAND" = 'open' ]; then
	if [ "$USER" = 'root' ]; then
		if [ -n "$OPT_AUSER" ]; then
			AUSER=$OPT_AUSER
		elif [ -z "$OPT_NOTINTERACTIVE" ]; then
			echo -n "User [$AUSER]: "
			read REPLY 
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
else
	echo "Type 'fix' or 'open' as command argument."
fi

exit 0

