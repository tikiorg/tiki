<?php
// $Header$

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');

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

$feed = "directories";
$title = tra("Tiki RSS feed for directory sites");
$desc = tra("Last sites.");
$now = date("U");
$id = "siteId";
$titleId = "name";
$descId = "description";
$dateId = "created";
$readrepl = "tiki-directory_redirect.php?$id=";
$uniqueid = $feed;

require ("tiki-rss_readcache.php");

if ($output == "EMPTY") {
  $changes = $tikilib->dir_list_all_valid_sites2(0, $max_rss_directories, $dateId.'_desc', '');
  $output = "";
}

require ("tiki-rss.php");

?>
