<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-wiki_rss.php,v 1.21 2003-10-12 12:22:52 ohertel Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');
include_once ('lib/wiki/histlib.php');

// object specific things:
if ($rss_wiki != 'y') {
	die; // TODO: output of rss file with message: rss disabled
}

if ($tiki_p_view != 'y') {
	$smarty -> assign('msg', tra("Permission denied you cannot view this section"));
	$smarty -> display("styles/$style_base/error.tpl");
	die; // TODO: output of rss file with message: permission denied
}

$title = "Tiki RSS feed for the wiki pages"; // TODO: make configurable
$desc = "Last modifications to the Wiki."; // TODO: make configurable
$now = date("U");
$id = "pageName";
$titleId = "pageName";
$descId = "comment";
$dateId = "lastModif";
$readrepl = $tikiIndex."?page=";
$changes = $histlib -> get_last_changes(999, 0, $max_rss_wiki, $sort_mode = $dateId.'_desc');
// FIX: get_last_changes does not return pages with german umlauts (they are left out)

require ("tiki-rss.php");

?>