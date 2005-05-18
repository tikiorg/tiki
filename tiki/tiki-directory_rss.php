<?php
// $Header$

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');
require_once('lib/directory/dirlib.php');
require_once ('lib/rss/rsslib.php');

if ($rss_directories != 'y') {
	$errmsg=tra("rss feed disabled");
	require_once ('tiki-rss_error.php');
}

if ($feature_directory != 'y') {
	$errmsg=tra("This feature is disabled").": feature_directory";
      require_once ('tiki-rss_error.php');
}

if ($tiki_p_view_directory != 'y') {
	$errmsg=tra("Permission denied");
      require_once ('tiki-rss_error.php');
}

if (!isset($_REQUEST["parent"])) {
        $errmsg=tra("No parent specified");
        require_once ('tiki-rss_error.php');
}

$feed = "directory";
$title = tra("Tiki RSS feed for directory sites");
$rc = $dirlib->dir_get_category($_REQUEST["parent"]);
$desc = tra("Last sites of directory ".$rc["name"]." .");
$now = date("U");
$id = "siteId";
$titleId = "name";
$descId = "description";
$dateId = "created";
$readrepl = "tiki-directory_redirect.php?$id=%s";
$uniqueid = $feed."?parent=".$_REQUEST["parent"];

$tmp = $tikilib->get_preference('title_rss_'.$feed, '');
if ($tmp<>'') $title = $tmp;
$tmp = $tikilib->get_preference('desc_rss_'.$feed, '');
if ($desc<>'') $desc = $tmp;

$changes = $dirlib->dir_list_sites($_REQUEST["parent"], 0, $max_rss_directories, $dateId.'_desc', '', 'y');
$output = $rsslib->generate_feed($feed, $uniqueid, '', $changes, $readrepl, '', $id, $title, $titleId, $desc, $descId, $dateId, '');

header("Content-type: ".$output["content-type"]);
print $output["data"];

?>
