#!/bin/sh
# $Header: /cvsroot/tikiwiki/tiki/doc/devtools/tikimerge_110to111.sh,v 1.1 2007-10-15 14:41:43 mose Exp $
#
# NOTE: Since 1.9.8 release, the 1.9 branch is frozen and merges are done manualy
# merge now only concerns BRANCH-1-10 to HEAD
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
# or "." (default) for all files

EXCLUDE="lang"
MERGE_LOGFILE="tikimerge.log.`date +%s`"

if [ -z $1 ]; then
	FILES="."
else
	FILES=$*
fi

if [ ! -f $MERGE_LOGFILE ]; then
	touch $MERGE_LOGFILE
fi

echo "# Start of block you can just copy-paste"
echo "# or adapt (especially comment of commit)"
echo ""
echo "# the merge should be done on tiki and not tikiwiki"
echo ""

echo "-------" >> $MERGE_LOGFILE
echo `date` >> $MERGE_LOGFILE
echo  >> $MERGE_LOGFILE

echo "tikimerge -? Launch an update of your local repository?"
echo -n "tikimerge -> [ press enter to proceed, ctrl-c to halt ] "
read 

echo "tikimerge -$ cvs -q up -d -r BRANCH-1-10 $FILES"
if [ "$FILES" = "." ]; then
	echo "tikimerge -> (please wait, this can take a while ...)" 
fi
#
# cvs -q up -d -r BRANCH-1-10 $FILES >> $MERGE_LOGFILE
echo "tikimerge -> cvs update done." 
echo

echo "tikimerge -? Move the merge tag ?" 
echo -n "tikimerge -> [ press enter to proceed, ctrl-c to halt ]"
read

echo "tikimerge -$ cvs -q tag -r BRANCH-1-10 -F BRANCH-1-10-HEAD $FILES"
#
# cvs -q tag -r BRANCH-1-10 -F BRANCH-1-10-HEAD $FILES >> $MERGE_LOGFILE
echo "tikimerge -> Tag moved done." 
echo

echo "tikimerge -? Grab the cvs HEAD ?"
echo -n "tikimerge -> [ press enter to proceed, ctrl-c to halt ]"
read

echo "tikimerge -$ cvs -q up -dA $FILES"
if [ "$FILES" = "." ]; then
	echo "tikimerge -> (please wait, this can take a while too ...)" 
fi
#
# cvs -q up -dA $FILES >> $MERGE_LOGFILE
echo "tikimerge -> cvs updated to HEAD." 
echo

echo "tikimerge -? Merge the files from BRANCH-1-10 ?"
echo -n "tikimerge -> [ press enter to proceed, ctrl-c to halt ]"
read

for i in $FILES; do
	echo "tikimerge -$ cvs -q up -dkk -j MERGE-1-10-HEAD -j BRANCH-1-10-HEAD $i"
	#
	# cvs -q up -dkk -j MERGE-1-10-HEAD -j BRANCH-1-10-HEAD $i >> $MERGE_LOGFILE
done
if [ "$FILES" = "." ]; then
	echo "tikimerge -> Excluded from merges : $EXCLUDE"
	for i in $EXCLUDE; do
		echo "tikimerge -$ rm -rf $i && cvs -q up -dA $i"
		#
		# rm -rf $i && cvs -q up -dA $i >> $MERGE_LOGFILE
	done
fi

echo "tikimerge -$ grep -r '<<<<<<<' $FILES"
# 
# grep -r '<<<<<<<' $FILES >> $MERGE_LOGFILE
echo "tikimerge -> Now get some fun fixing the conflicts if any are listed above."
echo -n "tikimerge -> [ press enter when done, ctrl-c to halt ]"
read

echo
echo "tikimerge -? Commit the merged files to HEAD ?"
echo -n "tikimerge -> [ press enter to proceed, ctrl-c to halt ]"
read

echo "tikimerge -$ cvs ci -m'Instant-Auto-Merge from BRANCH-1-10 to HEAD' $FILES"
# 
# cvs ci -m'Instant-Auto-Merge from BRANCH-1-10 to HEAD' $FILES >> $MERGE_LOGFILE

echo "tikimerge -> Auto-moving the floating tag to updated version."
echo "tikimerge -$ cvs -q tag -r BRANCH-1-10-HEAD -F MERGE-1-10-HEAD $FILES"

echo 
echo "tikimerge -> All Done."
echo 

exit 0

