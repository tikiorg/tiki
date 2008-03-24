<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-image_gallery_rss.php,v 1.31 2007-10-12 07:55:28 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');
require_once ('lib/imagegals/imagegallib.php');
require_once ('lib/rss/rsslib.php');

if ($prefs['rss_image_gallery'] != 'y') {
        $errmsg=tra("rss feed disabled");
        require_once ('tiki-rss_error.php');
}

if (!isset($_REQUEST["galleryId"])) {
        $errmsg=tra("No galleryId specified");
        require_once ('tiki-rss_error.php');
}

if ($tiki_p_view_image_gallery != 'y' or !$tikilib->user_has_perm_on_object($user,$_REQUEST['galleryId'],'image gallery','tiki_p_view_image_gallery')) {
        $errmsg=tra("Permission denied you cannot view this section");
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
	
        $tmp = $prefs['title_rss_'.$feed];
        if ($tmp<>'') $title = $tmp;
        $tmp = $prefs['desc_rss_'.$feed];
        if ($desc<>'') $desc = $tmp;
	
	$changes = $imagegallib->get_images( 0,$prefs['max_rss_image_gallery'],$dateId.'_desc', '', $_REQUEST["galleryId"]);
	$output = $rsslib->generate_feed($feed, $uniqueid, '', $changes, $readrepl, '', $id, $title, $titleId, $desc, $descId, $dateId, $authorId);
}
header("Content-type: ".$output["content-type"]);
print $output["data"];

?>
