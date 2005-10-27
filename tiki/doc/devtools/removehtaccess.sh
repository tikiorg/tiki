#!/bin/bash
# $Header: /cvsroot/tikiwiki/tiki/doc/devtools/removehtaccess.sh,v 1.2 2005-10-27 20:12:31 sylvieg Exp $
# Script to remove _htaccess which can be browsed unless hidden
# these files give an attacker useful information

if [ ! -d 'db' ]; then
        echo "You must launch this script from your (multi)tiki root dir."
        exit 0
fi
		

find . -name _htaccess -type f -exec rm -f {} \;

echo "Done."
