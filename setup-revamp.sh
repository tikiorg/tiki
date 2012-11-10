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
# This version is supposed to act almost the same way as setup.sh revision 43875
# if this script runs in production mode. Minor changes are:
# - default is to do nothing
# - debugging mode for further improvements
#   and adaption of permission check data
#   including a debugging option
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

ECHOFLAG=1 # one empty line before printing used options in debugging mode

# part 1 - preliminaries
# ----------------------

PERMISSIONCHECK_DIR="permissioncheck"
#USE_CASES_FILE="usecases.txt"
USE_CASES_FILE="usecases.bin"
USE_CASES_PATH=${PERMISSIONCHECK_DIR}
USE_CASES_NAME=${USE_CASES_PATH}/${USE_CASES_FILE}

# hint for users
POSSIBLE_COMMANDS='open|fix|nothing'
#HINT_FOR_USER="Type 'fix', 'nothing' or 'open' as command argument."
HINT_FOR_USER="\nType 'fix', 'nothing' or 'open' as command argument.
\nIf you used Tiki Permission Check via PHP, you know which of the following commands will probably work:
\ninsane mixed morepain moreworry pain paranoia paranoia-suphp risky worry\n"

usage() {
#usage: $0 [<switches>] open|fix
	cat <<EOF
usage: $0 [<switches>] ${POSSIBLE_COMMANDS}
-h           show help
-u user      owner of files (default: $AUSER)
-g group     group of files (default: $AGROUP)
-v virtuals  list of virtuals (for multitiki, example: "www1 www2")
-n           not interactive mode
-d off|on    disable|enable debugging mode (override script default)
EOF
}

# evaluate command line options (cannot be done inside a function)
set_debug() {
	case ${OPTARG} in
		off) DEBUG=0 ;;
		on) DEBUG=1 ;;
		*) DUMMY="no override, default remains active" ;;
	esac
}

OPT_AUSER=
OPT_AGROUP=
OPT_VIRTUALS=
OPT_NOTINTERACTIVE=

while getopts "hu:g:v:nd:" OPTION; do
	case $OPTION in
		h) usage ; exit 0 ;;
		u) OPT_AUSER=$OPTARG ;;
		g) OPT_AGROUP=$OPTARG ;;
		v) OPT_VIRTUALS=$OPTARG ;;
		n) OPT_NOTINTERACTIVE=1 ;;
		d) set_debug ;;
		?) usage ; exit 1 ;;
	esac
	if [ ${DEBUG} = '1' ] ; then
		if [ ${ECHOFLAG} = '1' ] ; then
			ECHOFLAG=0
			echo
		fi
		OUTPUT="option: -${OPTION}"
		if [ -n ${OPTARG} ] ; then
			OUTPUT="${OUTPUT} ${OPTARG}"
		fi
		echo ${OUTPUT}
	fi
done
shift $(($OPTIND - 1))

# define command to execute for main program
# default: do nothing
if [ -z $1 ]; then
	#COMMAND=fix
	COMMAND=nothing
else
	COMMAND=$1
fi

if [ ${DEBUG} = '1' ] ; then
	echo
	echo COMMAND: ${COMMAND}
fi

if [ ${DEBUG} = '1' ] ; then
	echo
	#echo usage output: begin
	usage
	#echo usage output: end
fi

define_path() {
# define PATH for executable mode
if [ ${DEBUG} = '1' ] ; then
	echo
	echo old path: ${PATH}
fi
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
if [ ${DEBUG} = '1' ] ; then
	echo new path: ${PATH}
fi
}

define_path

# set used commands
if [ ${DEBUG} = '1' ] ; then
	echo
	echo before:
	echo CAT=${CAT}
	echo CHGRP=${CHGRP}
	echo CHMOD=${CHMOD}
	echo CHOWN=${CHOWN}
	echo ID=${ID}
	echo MKDIR=${MKDIR}
	echo MV=${MV}
	echo RM=${RM}
	echo SORT=${SORT}
	echo TOUCH=${TOUCH}
	echo UNIQ=${UNIQ}
fi
# list of commands
CAT=`which cat`
CHGRP=`which chgrp`
CHMOD=`which chmod`
CHOWN=`which chown`
ID=`which id`
MKDIR=`which mkdir`
MV=`which mv`
RM=`which rm`
SORT=`which sort`
TOUCH=`which touch`
UNIQ=`which uniq`
if [ ${DEBUG} = '1' ] ; then
	echo after:
	echo CAT=${CAT}
	echo CHGRP=${CHGRP}
	echo CHMOD=${CHMOD}
	echo CHOWN=${CHOWN}
	echo ID=${ID}
	echo MKDIR=${MKDIR}
	echo MV=${MV}
	echo RM=${RM}
	echo SORT=${SORT}
	echo TOUCH=${TOUCH}
	echo UNIQ=${UNIQ}
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

get_permission_data() {
	if [ ${DEBUG} = '1' ] ; then
		echo
		echo permissioncheck subdir: ${PERMISSIONCHECK_DIR}
	fi
	#USE____CASES_FILE="${WORK_DIR}/usecases.txt"
	if [ -d ${USE_CASES_PATH} ] ; then
		if [ -f ${USE_CASES_NAME} ] ; then
			NO_MATCH=999
			MODEL_NAME=${NO_MATCH}
			MODEL_PERMS_SUBDIRS=${NO_MATCH}
			MODEL_PERMS_FILES=${NO_MATCH}
			while read ONE_USE_CASE_PER_LINE ; do
				USE_CASE=`echo ${ONE_USE_CASE_PER_LINE} | cut -d: -f1`
				if [ ${USE_CASE} = ${COMMAND} ] ; then
					MODEL_NAME=${USE_CASE}
					MODEL_PERMS_SUBDIRS=`echo ${ONE_USE_CASE_PER_LINE} | cut -d: -f2`
					MODEL_PERMS_FILES=`echo ${ONE_USE_CASE_PER_LINE} | cut -d: -f3`
					MODEL_PERMS_WRITE_SUBDIRS=`echo ${ONE_USE_CASE_PER_LINE} | cut -d: -f4`
					MODEL_PERMS_WRITE_FILES=`echo ${ONE_USE_CASE_PER_LINE} | cut -d: -f5`
					if [ ${DEBUG} = '1' ] ; then
						echo
						echo MODEL_NAME=${MODEL_NAME}
						echo MODEL_PERMS_SUBDIRS=${MODEL_PERMS_SUBDIRS}
						echo MODEL_PERMS_FILES=${MODEL_PERMS_FILES}
						echo MODEL_PERMS_WRITE_SUBDIRS=${MODEL_PERMS_WRITE_SUBDIRS}
						echo MODEL_PERMS_WRITE_FILES=${MODEL_PERMS_WRITE_FILES}
					fi
				fi
			done < ${USE_CASES_NAME}
			if [ ${MODEL_NAME} = ${NO_MATCH} ] ; then
					echo no matching use case found
					exit 1
			fi
		else
			echo ${USE_CASES_NAME} does not exist
			exit 1
		fi
	else
		echo ${USE_CASES_PATH} does not exist
		exit 1
	fi
}

permission_default() {
	${CHMOD} -fR u=rwX,go=rX .
}

permission_exceptions() {
	${CHMOD} o-rwx db/local.php
}

permission_via_php_check() {
	get_permission_data
 # set permissions here
 #echo "\nSTOP - DEBUG MODE"
 #exit 1
	if [ ${DEBUG} = '2' ] ; then
		echo
		find . -type d -exec echo ${CHMOD} ${MODEL_PERMS_SUBDIRS} {} \;
		find . -type f -exec echo ${CHMOD} ${MODEL_PERMS_FILES} {} \;
	fi
	E=echo
	$E find . -type d -exec ${CHMOD} ${MODEL_PERMS_SUBDIRS} {} \;
	$E find . -type f -exec ${CHMOD} ${MODEL_PERMS_FILES} {} \;
 #echo
	for WRITABLE in $DIRS ; do
 #echo ${WRITABLE}
		if [ -d ${WRITABLE} ] ; then
			$E find ${WRITABLE} -type d -exec echo ${CHMOD} ${MODEL_PERMS_WRITE_SUBDIRS} {} \;
			$E find ${WRITABLE} -type f -exec echo ${CHMOD} ${MODEL_PERMS_WRITE_FILES} {} \;
		fi
	done
 #echo "\nSTOP - DEBUG MODE"
 #exit 1
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

command_nothing() {
	echo 'Nothing done yet'
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
debug_exit() {
if [ ${DEBUG} = '1' ] ; then
	echo
	echo "Exiting... for execution mode use option '-d off' or set DEBUG=0 at the beginning of this script"
	echo
	exit 1
fi
}

# part 5 - main program
# ---------------------

case ${COMMAND} in
	# free defined
	fix)		command_fix ;;
	nothing)	command_nothing ;;
	open)		command_open ;;
	# Tiki Permission Check (via PHP)
	insane)		permission_via_php_check ;;
	mixed)		permission_via_php_check ;;
	morepain)	permission_via_php_check ;;
	moreworry)	permission_via_php_check ;;
	pain)		permission_via_php_check ;;
	paranoia)	permission_via_php_check ;;
	paranoia-suphp)	permission_via_php_check ;;
	php)		permission_via_php_check ;;
	risky)		permission_via_php_check ;;
	worry)		permission_via_php_check ;;
	foo) echo foo ;;
	*)		echo ${HINT_FOR_USER} ;;
esac

exit 0

# EOF
