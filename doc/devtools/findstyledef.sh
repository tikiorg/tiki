#!/bin/bash

# $Header: /cvsroot/tikiwiki/tiki/doc/devtools/findstyledef.sh,v 1.1 2005/03/13 23:38:07 ohertel Exp $
# finds all the style and class definitions in tpl and php files
#
# param needed for execution: rootdir of tiki
# 
# ohertel@tw.o

perl ./findstyles.pl $1 | sort | uniq > result.txt
