#!/bin/sh
# $Header: /cvsroot/tikiwiki/_mods/devtools/tikimergerev.sh,v 1.1 2004-05-09 23:43:37 damosoft Exp $
#
# That script is done for fast merging fixes that are done on branch
# refer to http://tikiwiki.org/tiki-index.php?page=CvsBranch18
# for explanaitoin of the process
#
# !   !   !   !   W A R N I N G   !   !   !   !   
# this script is very experimental and is not very clean nor finished.
# use it only if you know what all means
# remove echo if you are confident in automation
# but keep in mind that environment preservation requires consideration 
# so : Don't break the CVS !! :)
#
# -- mose
# 
# !   !   !   !   !   !   !   !   !   !   !   !
# 
# run it in branch to merge change to head instantly

# Use it giving file name(s) as arguments

if [ -z $1 ]; then
	echo "Usage: tikimerge.sh <files>"
	exit 0
fi

FILES=$*

echo "# Start of block you can just copy-paste"
echo "# or adapt (especially comment of commit)"

echo "cvs -q update -dP -r HEAD $FILES"
echo "cvs -q tag -r HEAD -F HEAD-BRANCH-1-8 $FILES"
echo "cvs -q up -r BRANCH-1-8-AdP $FILES"
for i in $FILES; do
	echo "cvs -q up -dkk -j MERGE-HEAD-to-BRANCH-1-8 -j HEAD-BRANCH-1-8 $i"
done
echo "cvs ci -m'Instant-Auto-Merge from HEAD to BRANCH' $FILES"
echo "cvs -q tag -r HEAD-BRANCH-1-8 -F MERGE-HEAD-BRANCH-1-8 $FILES"
echo "cvs -q up -r HEAD -dP $FILES"
echo "rm -f mergelog"

echo "# Done."
exit 0

