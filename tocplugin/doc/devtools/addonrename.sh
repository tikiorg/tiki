#!/bin/bash
# This is meant to be run from within the folder of the addon
# It is useful when forking git repositories

if [ $# -eq 0 ]
  then
    echo "Please specify from and to vendornames"
    exit
fi

FROM=$1
TO=$2

Q=$(find . -type f -name "*.php" -o -name "*.js" -o -name "*.css" -o -name "*.yml" -o -name "*.wiki" -o -name "*.tpl" -o -name "*.json")
for FILE in $Q; do
        sed -i.bak -e 's/'$FROM'\([_\/\.\-]\)/'$TO'\1/g' "$FILE"
done

X=$(find ./*/*)
for FILE in $X; do
	NEW="$(echo "$FILE" | sed 's/'$FROM'\([_\/\.\-]\)/'$TO'\1/')"
	if [ "$NEW" != "$FILE" ]; then
		git mv "$FILE" "$NEW"; 
	fi
done
Y=$(find ./*)
for FILE in $Y; do
        NEW="$(echo "$FILE" | sed 's/'$FROM'\([_\/\.\-]\)/'$TO'\1/')"
        if [ "$NEW" != "$FILE" ]; then
                git mv "$FILE" "$NEW";
        fi
done
Z=$(find .)
for FILE in $Z; do
        NEW="$(echo "$FILE" | sed 's/'$FROM'\([_\/\.\-]\)/'$TO'\1/')"
        if [ "$NEW" != "$FILE" ]; then
                git mv "$IN" "$FILE" "$NEW";
        fi
done
# Then run "git clean -f" to delete all unstaged files
# Make sure you keep a copy of this script first
# Then finally, commit all your changes
