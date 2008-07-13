#!/bin/bash

# This script tries to generate a tar file that contains only the files that
# have changed between the two versions.
# 

if [ -z "$1" -o -z "$2" ] ; then
	echo "To create a tar file that contains only the changed files:"
	echo "usage: `basename $0` dir_with_old_version dir_with_new_version"
	echo "the crated tar file will have the name"
	echo "diff-dir_with_old_version-dir_with_new_version.tar"
	exit
fi

tar --ignore-failed-read -cf diff-${1}-${2}.tar `diff -r --brief --new-file $1 $2 | awk '{print $4}'`  2> /dev/null

