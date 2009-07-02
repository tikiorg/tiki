<?php
// $Id: /cvsroot/tikiwiki/tiki/tiki-file_gallery_rss.php,v 1.34 2007-10-12 07:55:27 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');
require_once ('lib/rss/rsslib.php');

if ($prefs['rss_file_gallery'] != 'y') {
        $errmsg=tra("rss feed disabled");
        require_once ('tiki-rss_error.php');
}

if ($tiki_p_admin_file_galleries != 'y' and !$tikilib->user_has_perm_on_object($user,$_REQUEST['galleryId'],'file gallery','tiki_p_view_file_gallery')) {
        $errmsg=tra("Permission denied you cannot view this section");
        require_once ('tiki-rss_error.php');
}

if (!isset($_REQUEST["galleryId"])) {
        $errmsg=tra("No galleryId specified");
        require_once ('tiki-rss_error.php');
}

$feed = "filegal";
$uniqueid = "$feed.id=".$_REQUEST["galleryId"];
$output = $rsslib->get_from_cache($uniqueid);

if ($output["data"]=="EMPTY") {
	$tmp = $tikilib->get_file_gallery($_REQUEST["galleryId"]);
	$title = tra("Tiki RSS feed for the file gallery: ").$tmp["name"];
	$desc = $tmp["description"];
	$id = "fileId";
	$descId = "description";
	$dateId = "lastModif";
	$authorId = "user";
	$titleId = "filename";
	$readrepl = "tiki-download_file.php?$id=%s";
	if (($tmp["type"]=="podcast") || ($tmp["type"]=="vidcast")) {
		$titleId = "name";
		$readrepl = $prefs['fgal_podcast_dir']."%s";
		$id = "podcast_filename";
	}

	if ($title=="") {
        	$tmp = $prefs['title_rss_'.$feed];
       		if ($tmp<>'') $title = $tmp;
	}
	if ($desc=="") {
        	$tmp = $prefs['desc_rss_'.$feed];
	        if ($desc<>'') $desc = $tmp;
	}

	$changes = $tikilib->get_files( 0, $prefs['max_rss_file_gallery'], $dateId.'_desc', '', $_REQUEST["galleryId"]);
	$output = $rsslib->generate_feed($feed, $uniqueid, '', $changes, $readrepl, '', $id, $title, $titleId, $desc, $descId, $dateId, $authorId);
}
header("Content-type: ".$output["content-type"]);
print $output["data"];

?>
