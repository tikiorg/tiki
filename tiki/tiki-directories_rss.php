<?php
// $Header$

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');
require_once ('lib/rss/rsslib.php');

if ($prefs['rss_directories'] != 'y') {
	$errmsg=tra("rss feed disabled");
	require_once ('tiki-rss_error.php');
}

if ($prefs['feature_directory'] != 'y') {
	$errmsg=tra("This feature is disabled").": feature_directory";
      require_once ('tiki-rss_error.php');
}

if ($tiki_p_view_directory != 'y') {
	$errmsg=tra("Permission denied");
      require_once ('tiki-rss_error.php');
}

$feed = "directories";
$uniqueid = $feed;
$output = $rsslib->get_from_cache($uniqueid);

if ($output["data"]=="EMPTY") {
	$title = (!empty($desc_rss_directories)) ? $desc_rss_directories :tra("Tiki RSS feed for directory sites");
	$desc = (!empty($desc_rss_directories)) ? $desc_rss_directories :tra("Last sites.");
	$id = "siteId";
	$titleId = "name";
	$descId = "description";
	$dateId = "created";
	$readrepl = "tiki-directory_redirect.php?$id=%s";

        $tmp = $prefs['title_rss_'.$feed];
        if ($tmp<>'') $title = $tmp;
        $tmp = $prefs['desc_rss_'.$feed];
        if ($desc<>'') $desc = $tmp;

	$changes = $tikilib->dir_list_all_valid_sites2(0, $prefs['max_rss_directories'], $dateId.'_desc', '');
	$output = $rsslib->generate_feed($feed, $uniqueid, '', $changes, $readrepl, '', $id, $title, $titleId, $desc, $descId, $dateId, '');
}
header("Content-type: ".$output["content-type"]);
print $output["data"];

?>
