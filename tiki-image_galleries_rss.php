<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
$imagegallib = TikiLib::lib('imagegal');
$rsslib = TikiLib::lib('rss');

$access->check_feature('feature_galleries');

if ($prefs['feed_image_galleries'] != 'y') {
        $errmsg=tra("rss feed disabled");
        require_once ('tiki-rss_error.php');
}

$res=$access->authorize_rss(array('tiki_p_view_image_gallery','tiki_p_admin_galleries'));
if ($res) {
	if ($res['header'] == 'y') {
		header('WWW-Authenticate: Basic realm="'.$tikidomain.'"');
		header('HTTP/1.0 401 Unauthorized');
	}
	$errmsg=$res['msg'];
	require_once ('tiki-rss_error.php');
}

$feed = "imggal";
$uniqueid = $feed;
$output = $rsslib->get_from_cache($uniqueid);

if ($output["data"]=="EMPTY") {
	$title = $prefs['feed_image_galleries_title'];
	$desc = $prefs['feed_image_galleries_desc'];

	$id = "imageId";
	$titleId = "name";
	$descId = "description";
	$dateId = "created";
	$authorId = "user";
	$readrepl = "tiki-browse_image.php?imageId=%s";

	$changes = $imagegallib->list_images(0, $prefs['feed_image_galleries_max'], $dateId.'_desc', '');
	$output = $rsslib->generate_feed($feed, $uniqueid, '', $changes, $readrepl, '', $id, $title, $titleId, $desc, $descId, $dateId, $authorId);
}
header("Content-type: ".$output["content-type"]);
print $output["data"];
