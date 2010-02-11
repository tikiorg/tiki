<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');
require_once ('lib/rss/rsslib.php');

if ($prefs['rss_file_gallery'] != 'y') {
        $errmsg=tra("rss feed disabled");
        require_once ('tiki-rss_error.php');
}

if (empty($_REQUEST['galleryId'])) {
	$errmsg=tra("No galleryId specified");
	require_once ('tiki-rss_error.php');
}
if (!is_array($_REQUEST['galleryId'])) {
	$_REQUEST['galleryId'] = array( $_REQUEST['galleryId']);
}
$galleryIds = array();
foreach ($_REQUEST['galleryId'] as $fgalId) {
	if ($tiki_p_admin_file_galleries == 'y' || $tikilib->user_has_perm_on_object($user, $fgalId, 'file gallery', 'tiki_p_view_file_gallery')) {
		$galleryIds[] = $fgalId;
	}
}
if (empty($galleryIds)) {
	$errmsg=tra("Permission denied. You cannot view this section");
	require_once ('tiki-rss_error.php');
}

$feed = 'filegal';
$uniqueid = "$feed.id=".md5(implode('_', $galleryIds));
$output = $rsslib->get_from_cache($uniqueid);

if ($output["data"]=="EMPTY") {
	if (count($galleryIds) == 1) {
		$tmp = $tikilib->get_file_gallery($galleryIds[0]);
		$title = empty($prefs['title_rss_file_gallery'])? tra("Tiki RSS feed for the file gallery: "): $prefs['title_rss_file_gallery'];
		$title .= $tmp['name'];
        	$desc = empty($tmp['description'])? $prefs['desc_rss_file_gallery']: $tmp['description'];
	} else {
		$title = (!empty($prefs['title_rss_file_galleries'])) ? $prefs['title_rss_file_galleries'] : tra("Tiki RSS feed for file galleries");
		$desc = (!empty($prefs['desc_rss_file_galleries'])) ? $prefs['desc_rss_file_galleries'] : tra("Last files uploaded to the file galleries.");
	}
	$descId = "description";
	$dateId = "lastModif";
	$authorId = "user";
	$id = "fileId";
	$titleId = "filename";
	$readrepl = "tiki-download_file.php?$id=%s";
	if (($tmp["type"]=="podcast") || ($tmp["type"]=="vidcast")) {
		$titleId = "name";
		$readrepl = $prefs['fgal_podcast_dir']."%s";
		$id = "podcast_filename";
	} else {
		$id = "fileId";
		$titleId = "filename";
		$readrepl = "tiki-download_file.php?$id=%s";
	}

	if ($title=="") {
        	$tmp = $prefs['title_rss_'.$feed];
       		if ($tmp<>'') $title = $tmp;
	}
	if ($desc=="") {
        	$tmp = $prefs['desc_rss_'.$feed];
	        if ($desc<>'') $desc = $tmp;
	}

	$changes = $tikilib->get_files( 0, $prefs['max_rss_file_gallery'], $dateId.'_desc', '', $galleryIds);
	$output = $rsslib->generate_feed($feed, $uniqueid, '', $changes, $readrepl, '', $id, $title, $titleId, $desc, $descId, $dateId, $authorId);
}
header("Content-type: ".$output["content-type"]);
print $output["data"];
