<?php

// $Id$
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],'tiki-setup.php')!=FALSE) {
  header('location: index.php');
  exit;
}

// Wiki pagename regexp

if ( $prefs['wiki_page_regex'] == 'strict' ) $page_regex = '([A-Za-z0-9_])([\.: A-Za-z0-9_\-])*([A-Za-z0-9_])';
elseif ( $prefs['wiki_page_regex'] == 'full' ) $page_regex = '([A-Za-z0-9_]|[\x80-\xFF])([\.: A-Za-z0-9_\-]|[\x80-\xFF])*([A-Za-z0-9_]|[\x80-\xFF])';
else $page_regex = '([^\n|\(\)])((?!(\)\)|\||\n)).)*?';

// Wiki dump

$wiki_dump_exists = 'n';
$dump_path = 'dump';

if ( $tikidomain ) {
	$dump_path .= "/$tikidomain";
}
if ( file_exists($dump_path.'/new.tar') ) {
	$wiki_dump_exists = 'y';
}
$smarty->assign('wiki_dump_exists', $wiki_dump_exists);

// Wiki discussion

if ( $prefs['feature_wiki_discuss'] == 'y' ) {
	$wiki_discussion_string = $smarty->fetchLang($prefs['language'], 'wiki-discussion.tpl');
	$smarty->assign('wiki_discussion_string', $wiki_discussion_string);
}
