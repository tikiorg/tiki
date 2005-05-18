<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-tracker_rss.php,v 1.2 2005-05-18 10:58:59 mose Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');
require_once ('lib/trackers/trackerlib.php');
require_once ('lib/rss/rsslib.php');

if ($rss_tracker != 'y') {
        $errmsg=tra("rss feed disabled");
        require_once ('tiki-rss_error.php');
}

if ($feature_trackers != 'y') {
        $errmsg=tra("This feature is disabled").": feature_trackers";
        require_once ('tiki-rss_error.php');
}

if (!isset($_REQUEST["trackerId"])) {
        $errmsg=tra("No trackerId specified");
        require_once ('tiki-rss_error.php');
}

$feed = "tracker";
$tmp = $tikilib->get_tracker($_REQUEST["trackerId"]);

$title = tra("Tiki RSS feed for individual trackers: ").$tmp["name"];
$now = date("U");
$desc = $tmp["description"];
$tmp=null;

$tmp = $tikilib->get_preference('title_rss_'.$feed, '');
if ($tmp<>'') $title = $tmp;
$tmp = $tikilib->get_preference('desc_rss_'.$feed, '');
if ($tmp<>'') $desc = $tmp;

$id = "trackerId";
$titleId = "Subject";
$descId = ""; // "description";
$authorId = ""; // "user";
$dateId = "created";
$urlparam = "itemId";
$readrepl = "tiki-view_tracker_item.php?trackerId=%s&itemId=%s";
$uniqueid = "$feed.id=".$_REQUEST["trackerId"];

$tmp = $tikilib->list_tracker_items($_REQUEST["trackerId"], 0, $max_rss_tracker, $dateId.'_desc', '', '');
foreach ($tmp["data"] as $data) {
	$data[$titleId]='';
	foreach ($data["field_values"] as $data2) {
		if (isset($data2["name"])) {
			$data2["name"]=strtolower($data2["name"]);
			if ($data2["name"]=="subject") {
				$data[$titleId] = $data2["value"];
				break; // found a subject
			}
			// alternative names for subject field:
			if ($data[$titleId]=="") {
				if (($data2["name"]=="summary") ||
				    ($data2["name"]=="name") ||
				    ($data2["name"]=="title") ||
				    ($data2["name"]=="topic")) $data[$titleId] = $data2["value"];
				// no break here because we still might find a field called 'subject'
			}			
		}
	}
	$data["id"]=$_REQUEST["trackerId"];
	$data["field_values"]=null;

	$changes["data"][] = $data;
	$data=null;
}
$tmp=null;

$output = $rsslib->generate_feed($feed, $uniqueid, '', $changes, $readrepl, $urlparam, $id, $title, $titleId, $desc, $descId, $dateId, $authorId);
$changes=null;

header("Content-type: ".$output["content-type"]);
print $output["data"];

?>
