#!/bin/sh

# put list of virtual domains you need to support,
# if you have new domains to include, just add it 
# to the list and run that script again, it wouldn't 
# trash anything.
# also report those domains in db/tiki-db.php or better
# in db/local.php
#VIRTUALS="domain1.com domain2.net domain3.org"
VIRTUALS="feu.org cynt.org localis.org"
# that user is the apache user
USER="www-data" # maybe change it to "nobody"
# that group is you, if you need to access temp files
GROUP="mose" 

# comment next line when above lines are okay
# echo "Please configure the script first !" && exit 0

# -----------------------------------------------------------
# don't change anything below
DIRS="backups dump img/wiki img/wiki_up modules/cache temp templates_c"

for dir in $DIRS; do 
	if [ ! -d $dir ]; then 
		mkdir -p $dir
		echo "$dir missing ... created."
	else 
		echo "$dir ok"
	fi
	for vdir in $VIRTUALS; do 
		if [ ! -d "$dir/$vdir" ]; then
			mkdir -p "$dir/$vdir"
			echo "$dir/$vdir missing ... created."
		else 
			echo "$dir/$vdir ok"
		fi
	done
done

chown -R $USER $DIRS

if [ -n "$GROUP" ]; then 
	chgrp -R $GROUP $DIRS 
fi

chmod -R 02775 $DIRS

echo "Done."
