#!/bin/bash
 
# enable/disable .htaccess files
 
OLD=_htaccess
NEW=.htaccess
ACTION=activating
 
if [ "$1" = "off" ]; then
        OLD=.htaccess
        NEW=_htaccess
        ACTION=deactivating
fi
 
for i in $(find . -name ${OLD}); do
	chmod 644 $i
	echo "${ACTION} `dirname $i`/${NEW}"
	mv $i `dirname $i`/${NEW}
done
