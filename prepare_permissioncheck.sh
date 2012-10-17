#!/bin/bash

# we do _not_ use /bin/sh
# /bin/sh is a symlink to /bin/dash on Debian, dash doesn't know arrays

# ensure the command "which" is available
PATH="${PATH}:/bin:/usr/bin:/sbin:/usr/sbin:/usr/local/bin:/usr/local/sbin:/opt/bin:/opt/sbin:/opt/local/bin:/opt/local/sbin"
#echo -e ${PATH} "\n"
CHMOD=`which chmod`
#echo -e ${CHMOD} "\n"
PHP=`which php`
#echo -e ${PHP} "\n"

# compare with permissioncheck/usecases.php.inc

WORK_DIR="permissioncheck"
INDEX_FILE="index.php"
DEFAULT_FILE_NAME="check.php"

set -a NAME_LIST_SUBDIRS
set -a PERM_LIST_SUBDIRS
set -a PERM_LIST_FILES

# special case
NAME_LIST_SUBDIRS[0]="${WORK_DIR}"
PERM_LIST_SUBDIRS[0]=755
PERM_LIST_FILES[0]=644
#echo ${NAME_LIST_SUBDIRS[0]}

# the usecases
NAME_LIST_SUBDIRS[1]="paranoia"
PERM_LIST_SUBDIRS[1]=700
PERM_LIST_FILES[1]=600

NAME_LIST_SUBDIRS[2]="paranoia-suphp"
PERM_LIST_SUBDIRS[2]=701
PERM_LIST_FILES[2]=600

NAME_LIST_SUBDIRS[3]="mixed"
PERM_LIST_SUBDIRS[3]=770
PERM_LIST_FILES[3]=660

NAME_LIST_SUBDIRS[4]="risky"
PERM_LIST_SUBDIRS[4]=775
PERM_LIST_FILES[4]=664

# increase this number if you add usecases
MAX_USECASES=4

if [ "a" = "b" ] ; then
echo ${CHMOD} ${PERM_LIST_SUBDIRS[0]} "${NAME_LIST_SUBDIRS[0]}"
echo ${CHMOD} ${PERM_LIST_FILES[0]} "${NAME_LIST_SUBDIRS[0]}/${INDEX_FILE}"

for ((CASE_COUNTER=1; $CASE_COUNTER <= $MAX_USECASES; CASE_COUNTER++)) ; do
	#echo ${CASE_COUNTER}
	echo ${CHMOD} ${PERM_LIST_SUBDIRS[${CASE_COUNTER}]} "${WORK_DIR}/${NAME_LIST_SUBDIRS[${CASE_COUNTER}]}"
	echo ${CHMOD} ${PERM_LIST_FILES[${CASE_COUNTER}]} "${WORK_DIR}/${NAME_LIST_SUBDIRS[${CASE_COUNTER}]}/${DEFAULT_FILE_NAME}"
done
#else
#	echo no
fi

${CHMOD} ${PERM_LIST_SUBDIRS[0]} "${NAME_LIST_SUBDIRS[0]}"
${CHMOD} ${PERM_LIST_FILES[0]} "${NAME_LIST_SUBDIRS[0]}/${INDEX_FILE}"

for ((CASE_COUNTER=1; $CASE_COUNTER <= $MAX_USECASES; CASE_COUNTER++)) ; do
	#echo ${CASE_COUNTER}
	${CHMOD} ${PERM_LIST_SUBDIRS[${CASE_COUNTER}]} "${WORK_DIR}/${NAME_LIST_SUBDIRS[${CASE_COUNTER}]}"
	${CHMOD} ${PERM_LIST_FILES[${CASE_COUNTER}]} "${WORK_DIR}/${NAME_LIST_SUBDIRS[${CASE_COUNTER}]}/${DEFAULT_FILE_NAME}"
done

# quick 'n dirty
${CHMOD} 444 permissioncheck/permission_print.php.inc
${CHMOD} 444 permissioncheck/permission_granted.php.inc

#pwd
PERMISSION_GRANTED=`$PHP permissioncheck/permission_print.php.inc`
echo
echo permission to run permissioncheck: ${PERMISSION_GRANTED}
echo

# EOF
