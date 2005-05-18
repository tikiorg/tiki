<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-wiki_rss.php,v 1.32 2005-05-18 10:59:01 mose Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');
require_once ('lib/wiki/histlib.php');
require_once ('lib/rss/rsslib.php'); 

if ($rss_wiki != 'y') {
	$errmsg=tra("rss feed disabled");
	require_once ('tiki-rss_error.php');
}

if ($tiki_p_view != 'y') {
	$errmsg=tra("Permission denied you cannot view this section");
	require_once ('tiki-rss_error.php');
}

$feed = "wiki";
$title = (!empty($title_rss_wiki)) ? $title_rss_wiki : tra("Tiki RSS feed for the wiki pages");
$desc = (!empty($desc_rss_wiki)) ? $desc_rss_wiki : tra("Last modifications to the Wiki.");
$now = date("U");
$id = "pageName";
$titleId = "pageName";
$descId = "description";
$dateId = "lastModif";
$authorId = "user";
$readrepl = "tiki-index.php?page=%s";
$uniqueid = $feed;

$tmp = $tikilib->get_preference('title_rss_'.$feed, '');
if ($tmp<>'') $title = $tmp;
$tmp = $tikilib->get_preference('desc_rss_'.$feed, '');
if ($desc<>'') $desc = $tmp;

$changes = $tikilib -> list_pages(0, $max_rss_wiki, 'lastModif_desc');
$tmp = array();
foreach ($changes["data"] as $data)  {
	$data["$descId"] = $tikilib->parse_data($data["$descId"]);
	$tmp[] = $data;
}
$changes["data"] = $tmp;
$tmp = null;
$output = $rsslib->generate_feed($feed, $uniqueid, '', $changes, $readrepl, '', $id, $title, $titleId, $desc, $descId, $dateId, $authorId);

header("Content-type: ".$output["content-type"]);
print $output["data"];

?>
