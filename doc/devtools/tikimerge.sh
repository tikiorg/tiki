#!/bin/sh
# $Header: /cvsroot/tikiwiki/tiki/doc/devtools/tikimerge.sh,v 1.6 2004-08-16 02:26:42 teedog Exp $
#
# NOTE: Please start the merge process from BRANCH-1-8; don't start with 1-9
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

# Use it giving file name(s) as arguments: file name(s) can be particular files
# or "." for all files

if [ -z $1 ]; then
	echo "Usage: tikimerge.sh <files>"
	echo "<files> can be a file name like lib/tikilib.php or . for all files"
	exit 0
fi

FILES=$*

echo "# Start of block you can just copy-paste"
echo "# or adapt (especially comment of commit)"
echo "# NOTE: Please start the merge process from BRANCH-1-8; don't start with 1-9"
echo ""

echo "cvs -q up -dP -r BRANCH-1-8 $FILES"
echo "cvs -q tag -r BRANCH-1-8 -F BRANCH-1-8-HEAD $FILES"
echo "cvs -q up -dP -r BRANCH-1-9 $FILES"
for i in $FILES; do
	echo "cvs -q up -dkk -j MERGE-BRANCH-1-8-to-1-9 -j BRANCH-1-8-HEAD $i"
done
echo "grep -r '<<<<<' $FILES"
echo "cvs ci -m'Instant-Auto-Merge from BRANCH-1-8 to BRANCH-1-9' $FILES"
echo "cvs -q tag -r BRANCH-1-8-HEAD -F MERGE-BRANCH-1-8-to-1-9 $FILES"
echo "cvs -q tag -r BRANCH-1-9 -F BRANCH-1-9-HEAD $FILES"
echo "cvs -q up -AdP $FILES"
for i in $FILES; do
	echo "cvs -q up -dkk -j MERGE-BRANCH-1-9-to-HEAD -j BRANCH-1-9-HEAD $i"
done
echo "grep -r '<<<<<' $FILES"
echo "cvs ci -m'Instant-Auto-Merge from BRANCH-1-9 to HEAD' $FILES"
echo "cvs -q tag -r BRANCH-1-9-HEAD -F MERGE-BRANCH-1-9-to-HEAD $FILES"
echo "cvs -q up -r BRANCH-1-8 -dP $FILES"

echo "# Done."
exit 0

