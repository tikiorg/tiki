<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-wiki_rss.php,v 1.43.2.2 2008-01-17 17:52:22 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');
require_once ('lib/wiki/histlib.php');
require_once ('lib/rss/rsslib.php'); 

if ($prefs['rss_wiki'] != 'y') {
	$errmsg=tra("rss feed disabled");
	require_once ('tiki-rss_error.php');
}

if ($tiki_p_view != 'y') {
	$errmsg=tra("Permission denied you cannot view this section");
	require_once ('tiki-rss_error.php');
}

$feed = "wiki";
$uniqueid = $feed;
$output = $rsslib->get_from_cache($uniqueid);

if ($output["data"]=="EMPTY") {
	$title = (!empty($prefs['title_rss_wiki'])) ? $prefs['title_rss_wiki'] : tra("Tiki RSS feed for the wiki pages");
	$desc = (!empty($prefs['desc_rss_wiki'])) ? $prefs['desc_rss_wiki'] : tra("Last modifications to the Wiki.");
	$id = "pageName";
	$titleId = "pageName";
	$descId = "data";
	$dateId = "lastModif";
	$authorId = "user";
	// if param &diff=1 exists, link to diff, not to page itself
	if (isset($_REQUEST["diff"])) {
		$readrepl = "tiki-pagehistory.php?page=%s&compare=1&oldver=%s&newver=0&diff_style=minsidediff";
	} else {
		$readrepl = "tiki-index.php?page=%s";
	}
	$param = "previous";
	
	$changes = $tikilib -> list_pages(0, $prefs['max_rss_wiki'], 'lastModif_desc');
	$tmp = array();
	foreach ($changes["data"] as $data) {
		// get last 2 versions of the page and parse them
		$curr_page = $tikilib->get_page_info($data["pageName"]);
		$pageversion = (int)$histlib->get_page_latest_version($data["pageName"]);
		if ($pageversion==FALSE) {
			$prev_page = $curr_page;
			$prev_page["data"]="";
		} else {
			$prev_page = $histlib->get_page_from_history($data["pageName"], $pageversion, true);
		}
		$_REQUEST['redirectpage'] = 'y';//block the redirect interpretation 
		$curr_page_p = $tikilib->parse_data($curr_page["$descId"]);
		$prev_page_p = $tikilib->parse_data($prev_page["$descId"]);
	
		// do a diff between both pages
		require_once('lib/diff/difflib.php');
		$diff = diff2($prev_page_p , $curr_page_p, "unidiff");
	
		$result = "<style TYPE=\"text/css\"> .diffchar { color:red; } </style>";
		
		foreach ($diff as $part) {
			if ($part["type"]=="diffdeleted") {
				foreach ($part["data"] as $chunk) {
					$result.="- ".$chunk;
				}
			}
			if ($part["type"]=="diffadded") {
				foreach ($part["data"] as $chunk) {
					$result.="+ ".$chunk;
				}
			}
		}
		
		$data["$descId"] = $result;
	
		// hand over the version of the second page
		$data["$param"] = $prev_page["version"];
		$tmp[] = $data;
	}
	$changes["data"] = $tmp;
	
	$tmp = null;
	$output = $rsslib->generate_feed($feed, $uniqueid, '', $changes, $readrepl, $param, $id, $title, $titleId, $desc, $descId, $dateId, $authorId);
}
header("Content-type: ".$output["content-type"]);
print $output["data"];

?>
