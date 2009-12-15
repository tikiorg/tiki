#!/bin/sh


rm -f .lastup .svnrev
rm -f last.log
#cvs -z5 -q up -dP > last.log
svn update > last.log
echo `date +%s` > .lastup
svn info | grep ^Rev | awk '{print $2}' > .svnrev
less last.log

exit 0
