#!/bin/sh


rm -f .lastup
rm -f last.log
#cvs -z5 -q up -dP > last.log
svn update > last.log
echo `date +%s` > .lastup

less last.log

exit 0
