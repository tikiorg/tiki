<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');
require_once ('lib/imagegals/imagegallib.php');
require_once ('lib/rss/rsslib.php');

if ($prefs['feed_image_gallery'] != 'y') {
        $errmsg=tra("rss feed disabled");
        require_once ('tiki-rss_error.php');
}

if (!isset($_REQUEST["galleryId"])) {
        $errmsg=tra("No galleryId specified");
        require_once ('tiki-rss_error.php');
}

$tikilib->get_perm_object( $_REQUEST['galleryId'], 'image gallery' );

if ($tiki_p_view_image_gallery != 'y') {
	$smarty->assign('errortype', 401);
	$errmsg=tra("Permission denied. You cannot view this section");
	require_once ('tiki-rss_error.php');
}

$feed = "imggal";
$uniqueid = "$feed.id=".$_REQUEST["galleryId"];
$output = $rsslib->get_from_cache($uniqueid);

if ($output["data"]=="EMPTY") {
	$tmp = $imagegallib->get_gallery($_REQUEST["galleryId"]);
	$title = tra("Tiki RSS feed for the image gallery: ").$tmp["name"];
	$desc = $tmp["description"]; // TODO: make configurable
	$id = "imageId";
	$titleId = "name";
	$descId = "description";
	$authorId = "user";
	$dateId = "created";
	$readrepl = "tiki-browse_image.php?imageId=%s";
	
        $tmp = $prefs['feed_'.$feed.'_title'];
        if ($tmp<>'') $title = $tmp;
        $tmp = $prefs['feed_'.$feed.'_desc'];
        if ($desc<>'') $desc = $tmp;
	
	$changes = $imagegallib->get_images( 0,$prefs['feed_image_gallery_max'],$dateId.'_desc', '', $_REQUEST["galleryId"]);
	$output = $rsslib->generate_feed($feed, $uniqueid, '', $changes, $readrepl, '', $id, $title, $titleId, $desc, $descId, $dateId, $authorId);
}
header("Content-type: ".$output["content-type"]);
print $output["data"];
