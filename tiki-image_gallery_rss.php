<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-image_gallery_rss.php,v 1.17 2003-10-14 22:12:05 ohertel Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');
require_once ('lib/imagegals/imagegallib.php');

if ($rss_image_gallery != 'y') {
	$smarty -> assign('msg', tra("This feature is disabled"));
	$smarty -> display("styles/$style_base/error.tpl");
	die; // TODO: output of rss file with message: rss disabled
}

if ($tiki_p_view_image_gallery != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot view this section"));
  $smarty->display("styles/$style_base/error.tpl");
  die; // TODO: output of rss file with message: permission denied
}

if (!isset($_REQUEST["galleryId"])) {
	$smarty -> assign('msg', tra("No galleryId specified"));
	$smarty -> display("styles/$style_base/error.tpl");
	die; // TODO: output of rss file with message: object not found
}

$feed = "imggal";
$tmp = $imagegallib->get_gallery($_REQUEST["galleryId"]);
$title = "Tiki RSS feed for the image gallery: ".$tmp["name"]; // TODO: make configurable
$now = date("U");
$desc = $tmp["description"]; // TODO: make configurable
$id = "imageId";
$titleId = "name";
$descId = "description";
$dateId = "created";
$readrepl = "tiki-browse_image.php?imageId=";

require ("tiki-rss_readcache.php");

if ($output == "EMPTY") {
  $changes = $imagegallib->get_images( 0,$max_rss_image_gallery,$dateId.'_desc', '', $_REQUEST["galleryId"]);
  $output = "";
}

require ("tiki-rss.php");

?>