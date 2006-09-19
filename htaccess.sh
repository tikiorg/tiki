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
 
ENTRIES=`/bin/ls -Rd *`
for ENTRY in ${ENTRIES}
do
        # only work on directories:
        if [ -d "$ENTRY" ]; then
                # do nothing if $NEW already available
                if [ ! -e ${ENTRY}/${NEW} ]; then
                        # if $OLD available, rename it
                        if [ -e ${ENTRY}/${OLD} ]; then
                                echo "${ACTION} ${ENTRY}/.htaccess"
                                chmod 644 ${ENTRY}/${OLD}
                                mv ${ENTRY}/${OLD} ${ENTRY}/${NEW}
                        fi
                fi
        fi
done
