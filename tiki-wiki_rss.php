<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-wiki_rss.php,v 1.25 2003-10-20 15:48:17 ohertel Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');
include_once ('lib/wiki/histlib.php');

if ($rss_wiki != 'y') {
	die; // TODO: output of rss file with message: rss disabled
}

if ($tiki_p_view != 'y') {
	$smarty -> assign('msg', tra("Permission denied you cannot view this section"));
	$smarty -> display("styles/$style_base/error.tpl");
	die; // TODO: output of rss file with message: permission denied
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

require ("tiki-rss_readcache.php");

if ($output == "EMPTY") {
  $changes = $tikilib -> list_pages(0, $max_rss_wiki, 'lastModif_desc');
  // FIX: get_last_changes does not return pages with german umlauts (they are left out)
  $output = "";
}

require ("tiki-rss.php");

?>