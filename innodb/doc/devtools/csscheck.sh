#!/bin/bash
# (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
# 
# All Rights Reserved. See copyright.txt for details and a complete list of authors.
# Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
# $Id$

# Provide a report on CSS classes used in a stylesheet 
# Usage: ./csscheck.sh tikineat

# remove { anything }
# remove comments
# break groupings
# remove whitespace before
# remove whiltespace after
# remove blank lines

# grep -r '{$helpurl}' templates | sed -e "s/^.*{$helpurl}\([^\"']*\)[\"'].*$/\1/" | sort | uniq | awk '{print "(("$1"))~|~ ~|~"}' | tr "+" " "

# grep -r '^[A-Za-z\.\#0-9].*{' ../styles/mose.css | sed -e "s/^\([A-Za-z0-9]+\).*{$/\1/" | grep -v "{" | sort | uniq

./stripbraces.pl ../../styles/$1.css \
| ./stripcomments.pl \
| sed -e "s/,[ 	]*/\n/g;" \
| sed -e "s/^[ 	]*//g;" \
| sed -e "s/[ 	]*$//g;" \
| egrep -v "^[ 	]*$" \
| sort \
| uniq

# grep -v "^[   ]*{.*}[         ]*$" \
# ^.*;+[   ]*$ | ^[  ]*[{}][         ]*$
