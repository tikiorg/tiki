#!/bin/sh

ACTION=$1
#echo ${ACTION}

# ensure the command "which" is available
PATH="${PATH}:/bin:/usr/bin:/sbin:/usr/sbin:/usr/local/bin:/usr/local/sbin:/opt/bin:/opt/sbin:/opt/local/bin:/opt/local/sbin"
#echo -e ${PATH} "\n"
CHMOD=`which chmod`
#echo -e ${CHMOD} "\n"
PHP=`which php`
#echo -e ${PHP} "\n"
COPY=`which cp`

# compare with permissioncheck/usecases.php.inc

WORK_DIR="permissioncheck"
INDEX_FILE="index.php"
DEFAULT_FILE_NAME="check.php"
USECASES_FILE="${WORK_DIR}/usecases.txt"
GRANT="${WORK_DIR}/permission_granted.txt"
NO="${WORK_DIR}/no.txt"
YES="${WORK_DIR}/yes.txt"

# quick 'n dirty
# none of those permissions is critical
#
${CHMOD} 644 "${GRANT}"
${CHMOD} 644 "${NO}"
${CHMOD} 644 "${YES}"
#
${CHMOD} 755 "${WORK_DIR}/"
${CHMOD} 644 "${WORK_DIR}/${DEFAULT_FILE_NAME}"
${CHMOD} 644 "${WORK_DIR}/functions.php.inc"
${CHMOD} 600 "${WORK_DIR}/_htaccess"
${CHMOD} 600 "${WORK_DIR}/.htpasswd"
${CHMOD} 644 "${WORK_DIR}/index.php"
${CHMOD} 444 "${WORK_DIR}/permission_print.php.inc"
${CHMOD} 644 "${WORK_DIR}/permission_granted.php.inc"
${CHMOD} 644 "${WORK_DIR}/usecases.php.inc"
${CHMOD} 644 "${WORK_DIR}/usecases.txt"

phpcheck() {
#pwd
PHP_PERMISSION_GRANTED=`$PHP ${WORK_DIR}/permission_print.php.inc`
echo
echo permission to run permissioncheck: ${PHP_PERMISSION_GRANTED}
echo
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
	*)
		echo "Usage: sh prepare_permissioncheck.sh {disable|enable}"
		;;
esac

# EOF
