<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-file_gallery_rss.php,v 1.23 2004-07-15 22:09:07 teedog Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');

if ($rss_file_gallery != 'y') {
        $errmsg=tra("rss feed disabled");
        require_once ('tiki-rss_error.php');
}

if ($tiki_p_view_file_gallery != 'y') {
        $errmsg=tra("Permission denied you cannot view this section");
        require_once ('tiki-rss_error.php');
}

if (!isset($_REQUEST["galleryId"])) {
        $errmsg=tra("No galleryId specified");
        require_once ('tiki-rss_error.php');
}

$feed = "filegal";
global $filegallib;
if (!is_object($filegallib)) {
	include_once ('lib/filegals/filegallib.php');
}
$tmp = $filegallib->get_file_gallery($_REQUEST["galleryId"]);
$title = tra("Tiki RSS feed for the file gallery: ").$tmp["name"];
$desc = $tmp["description"];
$now = date("U");
$id = "fileId";
$descId = "description";
$dateId = "created";
$titleId = "filename";
$readrepl = "tiki-download_file.php?$id=";
$uniqueid = "$feed.id=".$_REQUEST["galleryId"];

require ("tiki-rss_readcache.php");

if ($output == "EMPTY") {
  $changes = $tikilib->get_files( 0,10,$dateId.'_desc', '', $_REQUEST["galleryId"]);
  $output = "";
}

require ("tiki-rss.php");

?>
