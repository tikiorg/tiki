<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');
require_once ('lib/rss/rsslib.php');

$access->check_feature('feature_file_galleries');

if ($prefs['rss_file_galleries'] != 'y') {
        $errmsg=tra("rss feed disabled");
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
	$authorId = "lastModifUser";
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
