<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-wiki_rss.php,v 1.27 2004-03-07 23:12:01 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');
include_once ('lib/wiki/histlib.php');

if ($rss_wiki != 'y') {
	$errmsg=tra("rss feed disabled");
	require_once ('tiki-rss_error.php');
}

if ($tiki_p_view != 'y') {
	$errmsg=tra("Permission denied you cannot view this section");
	require_once ('tiki-rss_error.php');
}

$feed = "wiki";
$title = "Tiki RSS feed for the wiki pages"; // TODO: make configurable
$desc = "Last modifications to the Wiki."; // TODO: make configurable
$now = date("U");
$id = "pageName";
$titleId = "pageName";
$descId = "comment";
$dateId = "lastModif";
$readrepl = "tiki-index.php?page=";
$uniqueid = $feed;

require ("tiki-rss_readcache.php");

if ($output == "EMPTY") {
  $changes = $tikilib -> list_pages(0, $max_rss_wiki, 'lastModif_desc');
  // FIX: get_last_changes does not return pages with german umlauts (they are left out)
  $output = "";
}

require ("tiki-rss.php");

?>
