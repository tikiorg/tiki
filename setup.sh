#!/bin/sh

if [ -z "$1" ];
then
	cat <<EOF
Usage $0 user [group]

For example, if apache is running as user nobody, type:

  su -c '$0 nobody'

Alternatively, you may wish to set both the user and group:
  
  su -c '$0 mylogin nobody'
EOF
exit 1
fi

if [ ! -d modules/cache ];
then
	mkdir -p modules/cache
fi
if [ ! -d templates_c ];
then
	mkdir -p templates_c
fi


chown -R $1 modules/cache 
chown -R $1 templates_c
chown -R $1 img/wiki_up
chown -R $1 img/wiki



if [ -n "$2" ];
then
	chgrp -R $2 modules/cache templates_c
	chmod g+w modules/cache templates_c
fi

