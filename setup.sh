#!/bin/sh

DIRS="backups dump img/wiki img/wiki_up modules/cache temp templates_c"

if [ -z "$1" ];
then
	cat <<EOF
Usage $0 user [group]

For example, if apache is running as user nobody, type:

  su -c '$0 nobody'

Alternatively, you may wish to set both the user and group:
  
  su -c '$0 yourlogin nobody'

This will allow you to delete certain files/directories without becoming root.
  
Or, if you can't become root, but are a member of the group apache runs under
(for example: nobody), you can type:

  $0 yourlogin nobody
  
EOF
exit 1
fi

for dir in $DIRS
do
	if [ ! -d $dir ]
	then
		mkdir -p $dir
	fi
done

chown -R $1 $DIRS

if [ -n "$2" ];
then
	chgrp -R $2 $DIRS
fi

chmod -R 02775 $DIRS
