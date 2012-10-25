#!/bin/sh

ACTION=$1

# ensure the command "which" is available
PATH="${PATH}:/bin:/usr/bin:/sbin:/usr/sbin:/usr/local/bin:/usr/local/sbin:/opt/bin:/opt/sbin:/opt/local/bin:/opt/local/sbin"
CHMOD=`which chmod`
COPY=`which cp`

# compare with permissioncheck/usecases.inc.php

WORK_DIR="permissioncheck"
INDEX_FILE="index.php"
DEFAULT_FILE_NAME="check.php"
USECASES_FILE="${WORK_DIR}/usecases.txt"
GRANT="${WORK_DIR}/permission_granted.bin"
NO="${WORK_DIR}/no.bin"
YES="${WORK_DIR}/yes.bin"
HTACCESS="${WORK_DIR}/.htaccess"

# quick 'n dirty
# none of those permissions are critical
#
${CHMOD} 755 "${WORK_DIR}/"
#
${CHMOD} 644 "${GRANT}"
${CHMOD} 444 "${NO}"
${CHMOD} 444 "${YES}"
#
${CHMOD} 644 "${WORK_DIR}/${DEFAULT_FILE_NAME}"
${CHMOD} 644 "${WORK_DIR}/functions.inc.php"
if [ -f ${HTACCESS} ] ; then
	${CHMOD} 644 ${HTACCESS}
fi
${CHMOD} 600 "${WORK_DIR}/_htaccess"
${CHMOD} 600 "${WORK_DIR}/.htpasswd"
${CHMOD} 644 "${WORK_DIR}/${INDEX_FILE}"
${CHMOD} 644 "${WORK_DIR}/permission_granted.inc.php"
${CHMOD} 644 "${WORK_DIR}/usecases.inc.php"
#${CHMOD} 644 "${WORK_DIR}/usecases.txt"
${CHMOD} 644 "${USECASES_FILE}"

disable_perm_check() {
while read line_of_file_orig ; do
	${COPY} ${NO} ${GRANT}
	#echo $line_of_file_orig
	usecase=`echo $line_of_file_orig | cut -d: -f1`
	#echo $usecase
	uc_perms_subdir=`echo $line_of_file_orig | cut -d: -f2`
	#echo $uc_perms_subdir
	uc_perms_file=`echo $line_of_file_orig | cut -d: -f3`
	#echo $uc_perms_file
	${CHMOD} 700 ${WORK_DIR}/${usecase}
	#ls -ld ${WORK_DIR}/${usecase}
	${CHMOD} 600 ${WORK_DIR}/${usecase}/${DEFAULT_FILE_NAME}
	#ls -l ${WORK_DIR}/${usecase}/${DEFAULT_FILE_NAME}
	#echo
done < ${USECASES_FILE}
}

enable_perm_check() {
while read line_of_file_orig ; do
	${COPY} ${YES} ${GRANT}
	#echo $line_of_file_orig
	usecase=`echo $line_of_file_orig | cut -d: -f1`
	#echo $usecase
	uc_perms_subdir=`echo $line_of_file_orig | cut -d: -f2`
	#echo $uc_perms_subdir
	uc_perms_file=`echo $line_of_file_orig | cut -d: -f3`
	#echo $uc_perms_file
	${CHMOD} ${uc_perms_subdir} ${WORK_DIR}/${usecase}
	#ls -ld ${WORK_DIR}/${usecase}
	${CHMOD} ${uc_perms_file} ${WORK_DIR}/${usecase}/${DEFAULT_FILE_NAME}
	#ls -l ${WORK_DIR}/${usecase}/${DEFAULT_FILE_NAME}
	#echo
done < ${USECASES_FILE}
}


case ${ACTION} in
	disable)
		disable_perm_check
		;;
	enable)
		enable_perm_check
		;;
	*)
		echo "Usage: sh prepare_permissioncheck.sh {disable|enable}"
		;;
esac

# EOF
