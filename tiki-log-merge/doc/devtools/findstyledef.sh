#!/bin/bash

# $Id$
# finds all the style and class definitions in tpl and php files
#
# param needed for execution: rootdir of tiki
# 
# ohertel@tw.o

perl ./findstyles.pl $1 | sort | uniq > result.txt
