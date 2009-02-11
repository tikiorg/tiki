svn status | awk '/^[UACDM]/ {print $2}' | xargs svn revert > /dev/null 2> /dev/null
svn status | awk '/^\?/ {print $2}' | xargs rm > /dev/null 2> /dev/null
