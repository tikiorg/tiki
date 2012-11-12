#!/bin/sh

ACTION=$1

# all filenames concerning permission
# check must not include a colon `:'

# ensure the command "which" is available
PATH="${PATH}:/bin:/usr/bin:/sbin:/usr/sbin:/usr/local/bin:/usr/local/sbin:/opt/bin:/opt/sbin:/opt/local/bin:/opt/local/sbin"
CHMOD=`which chmod`
COPY=`which cp`
MKDIR=`which mkdir`

# compare with permissioncheck/usecases.inc.php

WORK_DIR="permissioncheck"
LIST_OF_FILES="${WORK_DIR}/list-of-files.txt"
LIST_OF_SUBDIRS="${WORK_DIR}/list-of-subdirs.txt"
#
INDEX_FILE="index.php"
DEFAULT_FILE_NAME="check.php"
#USECASES_FILE="${WORK_DIR}/usecases.txt"
USECASES_FILE="${WORK_DIR}/usecases.bin"
GRANT="${WORK_DIR}/permission_granted.bin"
NO="${WORK_DIR}/no.bin"
YES="${WORK_DIR}/yes.bin"
HTACCESS="${WORK_DIR}/.htaccess"

# quick 'n dirty
# none of those permissions are critical
#
if [ -d ${WORK_DIR} ] ; then
	${CHMOD} 755 "${WORK_DIR}"
else
	echo "${WORK_DIR} does not exist"
	exit 1
fi
if [ -f ${LIST_OF_FILES} ] ; then
	${CHMOD} 644 "${LIST_OF_FILES}"
else
	echo "${LIST_OF_FILES} does not exist"
	exit 1
fi
# the next may be redundant, because it could be done in ${LIST_OF_FILES}
if [ -f ${LIST_OF_SUBDIRS} ] ; then
	${CHMOD} 644 "${LIST_OF_SUBDIRS}"
else
	echo "${LIST_OF_SUBDIRS} does not exist"
	exit 1
fi

# hardcoded permissions are supposed to be
# removed and replaced by list of files
hardcoded_perms() {
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
	#${CHMOD} 644 "${WORK_DIR}/usecases.bin"
	#${CHMOD} 644 "${WORK_DIR}/usecases.txt"
	${CHMOD} 644 "${USECASES_FILE}"
}
hardcoded_perms

dynamic_perms_files() {
echo ' dynamic_perms_files'
while read line_of_file_orig ; do
	static_file_name="permissioncheck/"`echo $line_of_file_orig | cut -d: -f1`
	#echo $static_file_name
	static_file_perm=`echo $line_of_file_orig | cut -d: -f2`
	#echo $static_file_perm
	if [ -f $static_file_name ] ; then
		#echo ${CHMOD} $static_file_perm $static_file_name
		${CHMOD} $static_file_perm $static_file_name
	else
		echo "$static_file_name $static_file_perm does not exist"
		echo exit 1 recommended
	fi
done < ${LIST_OF_FILES}
}

dynamic_perms_subdirs() {
echo ' dynamic_perms_subdirs'
while read line_of_file_orig ; do
	static_subdir_name="permissioncheck/"`echo $line_of_file_orig | cut -d: -f1`
	#echo $static_file_name
	static_subdir_perm=`echo $line_of_file_orig | cut -d: -f2`
	#echo $static_file_perm
	if [ -d $static_subdir_name ] ; then
		#echo ${CHMOD} $static_subdir_perm $static_subdir_name
		${CHMOD} $static_subdir_perm $static_subdir_name
	else
		echo "$static_subdir_name $static_subdir_perm does not exist"
		echo ${MKDIR} $static_subdir_name '#' recommended
		#echo exit 1 recommended
	fi
done < ${LIST_OF_SUBDIRS}
}

dynamic_perms() {
	dynamic_perms_files
	dynamic_perms_subdirs
}

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
	test)
		dynamic_perms
		;;
	*)
		echo "Usage: sh prepare_permissioncheck.sh {disable|enable}"
		;;
esac

# EOF
