#!/bin/bash
# (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
# 
# All Rights Reserved. See copyright.txt for details and a complete list of authors.
# Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
# $Id$

# script to strip all comments and whitespace from
# php sources. Gives a very small speedup on hosts
# without php accelerators (like turck mmcache).
#
# just go to your tiki dir and execute
# doc/devtools/phpstrip.sh

function phpstrip { 
php -w $1 > ${1}.w; 
mv -f ${1}.w $1; 
}

A=`find . -name "*.php"`

for i in $A ; do
  phpstrip $i;
done

