<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');

$imagegallib = TikiLib::lib('imagegal');
$rsslib = TikiLib::lib('rss');

if ($prefs['feed_image_gallery'] != 'y') {
	$errmsg = tra('rss feed disabled');
	require_once ('tiki-rss_error.php');
}

if (!isset($_REQUEST['galleryId'])) {
	$errmsg = tra('No gallery ID specified');
	require_once ('tiki-rss_error.php');
}

$tikilib->get_perm_object($_REQUEST['galleryId'], 'image gallery');

if ($tiki_p_view_image_gallery != 'y') {
	$smarty->assign('errortype', 401);
	$errmsg = tra('You do not have permission to view this section');
	require_once ('tiki-rss_error.php');
}

$feed = 'imggal';
$uniqueid = "$feed.id=" . $_REQUEST['galleryId'];
$output = $rsslib->get_from_cache($uniqueid);

if ($output['data'] == 'EMPTY') {
	$tmp = $imagegallib->get_gallery($_REQUEST['galleryId']);
	$title = $prefs['feed_image_gallery_title'] . $tmp['name'];
	$desc = $prefs['feed_image_gallery_desc'] . $tmp['description'];
	$id = 'imageId';
	$titleId = 'name';
	$descId = 'description';
	$authorId = 'user';
	$dateId = 'created';
	$readrepl = 'tiki-browse_image.php?imageId=%s';
	
	$changes = $imagegallib->get_images(0, $prefs['feed_image_gallery_max'], $dateId . '_desc', '', $_REQUEST['galleryId']);
	$output = $rsslib->generate_feed($feed, $uniqueid, '', $changes, $readrepl, '', $id, $title, $titleId, $desc, $descId, $dateId, $authorId);
}
header('Content-type: ' . $output['content-type']);
print $output['data'];
