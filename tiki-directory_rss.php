<?php
// $Header$

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');
include_once('lib/directory/dirlib.php');

if ($rss_directory != 'y') {
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
$readrepl = "tiki-directory_redirect.php?$id=";
$uniqueid = $feed."?parent=".$_REQUEST["parent"];

require ("tiki-rss_readcache.php");

if ($output == "EMPTY") {
  $changes = $dirlib->dir_list_sites($_REQUEST["parent"], 0, $max_rss_directory, $dateId.'_desc', '', 'y');
  $output = "";
}

require ("tiki-rss.php");

?>
