<?php
// $Id: /cvsroot/tikiwiki/tiki/tiki-file_galleries_rss.php,v 1.32 2007-10-12 07:55:27 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');
require_once ('lib/rss/rsslib.php');

if ($prefs['rss_file_galleries'] != 'y') {
        $errmsg=tra("rss feed disabled");
        require_once ('tiki-rss_error.php');
}

if ($tiki_p_view_file_gallery != 'y') {
	$smarty->assign('errortype', 401);
	$errmsg=tra("Permission denied you cannot view this section");
	require_once ('tiki-rss_error.php');
}

$feed = "filegals";
$uniqueid = $feed;
$output = $rsslib->get_from_cache($uniqueid);

if ($output["data"]=="EMPTY") {
	$title = (!empty($title_rss_file_galleries)) ? $title_rss_file_galleries : tra("Tiki RSS feed for file galleries");
	$desc = (!empty($desc_rss_file_galleries)) ? $desc_rss_file_galleries : tra("Last files uploaded to the file galleries.");
	$id = "fileId";
	$descId = "description";
	$dateId = "lastModif";
	$authorId = "user";
	$titleId = "filename";
	$readrepl = "tiki-download_file.php?$id=%s";

        $tmp = $prefs['title_rss_'.$feed];
        if ($tmp<>'') $title = $tmp;
        $tmp = $prefs['desc_rss_'.$feed];
        if ($desc<>'') $desc = $tmp;

	$changes = $tikilib->list_files(0, $prefs['max_rss_file_galleries'], $dateId.'_desc', '');
	$output = $rsslib->generate_feed($feed, $uniqueid, '', $changes, $readrepl, '', $id, $title, $titleId, $desc, $descId, $dateId, $authorId);
}
header("Content-type: ".$output["content-type"]);
print $output["data"];

?>		
