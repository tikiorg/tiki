#!/bin/sh


rm -f .lastup
rm -f last.log
cvs -q up -dP > last.log
echo `date +%s` > .lastup

less last.log

exit 0
