#!/bin/bash
# (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
# 
# All Rights Reserved. See copyright.txt for details and a complete list of authors.
# Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
# $Id$

# that script prepares a dump of tiki for selective duplication
# tested on debian only for now
# 
# mose@tw.o

RHOST="dev.tiki.org"
RTIKI="/usr/local/tikidev"
RTMPDIR="/tmp"
ARCHDIR="/usr/local/tikidev/duplidev"
VIRTUAL="dev.tiki.org"

OLDIR=`pwd`

eval `cat $RTIKI/db/$VIRTUAL/local.php | sed -e '/[\?#]/d' -e "s/\$\([-_a-z]*\)[[:space:]]*=[[:space:]]*\([-_a-zA-Z0-9\"'\.]*\);/\\1=\\2/"`
RDBHOST=${host_tiki:-'localhost'}
RDBNAME=${dbs_tiki:-'tikiwiki'}
RDBUSER=${user_tiki:-'root'}
RDBPASS=${pass_tiki:-''}

DATE=`date +%Y-%m-%d`
DUMP="dev.tiki.org.$DATE.sql"
DUMPLIGHT="dev.tiki.org_light.$DATE.sql"
cd $ARCHDIR

mysqldump -e -f --add-drop-table -h$RDBHOST -u$RDBUSER -p$RDBPASS $RDBNAME tiki_pages > $DUMPLIGHT
mysqldump -e -f --add-drop-table -h$RDBHOST -u$RDBUSER -p$RDBPASS $RDBNAME \
	tiki_calendar_categories \
	tiki_calendar_items \
	tiki_calendar_locations \
	tiki_calendar_roles \
	tiki_calendars \
	tiki_categories \
	tiki_categorized_objects \
	tiki_category_objects \
	tiki_category_sites \
	tiki_comments \
	tiki_drawings \
	tiki_extwiki \
	tiki_menus  \
	tiki_menu_options  \
	tiki_modules  \
	tiki_pages  \
	tiki_quicktags  \
	tiki_related_categories  \
	tiki_rss_feeds  \
	tiki_rss_modules  \
	tiki_structures  \
	tiki_wiki_attachments  \
	tiki_tracker_fields \
	tiki_tracker_item_attachments  \
	tiki_tracker_item_comments  \
	tiki_tracker_item_fields  \
	tiki_tracker_items  \
	tiki_tracker_options  \
	tiki_trackers  \
	> $DUMP

bzip2 $DUMP
bzip2 $DUMPLIGHT

cd $OLDIR
echo "Done."

exit 0
