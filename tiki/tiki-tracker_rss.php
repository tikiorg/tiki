<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-tracker_rss.php,v 1.3 2006-02-17 15:10:31 sylvieg Exp $

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
$titleId = "rss_subject";
$descId = "rss_description";
$authorId = ""; // "user";
$dateId = "created";
$urlparam = "itemId";
$readrepl = "tiki-view_tracker_item.php?trackerId=%s&itemId=%s";
$uniqueid = "$feed.id=".$_REQUEST["trackerId"];

$tmp = $tikilib->list_tracker_items($_REQUEST["trackerId"], 0, $max_rss_tracker, $dateId.'_desc', '', '');
foreach ($tmp["data"] as $data) {
	$data[$titleId] = tra('Tracker item:').' #'.$data["itemId"];
	$data[$descId] = '';
	$first_text_field = null;
	$aux_subject = null;
	foreach ($data["field_values"] as $data2) {
		if (isset($data2["name"])) {
			if ($data2["type"] != "e") {
				if ($data2["value"] == "") $data2["value"] = "(".tra('empty').")";
				$data[$descId] .= $data2["name"].": ".$data2["value"]."<br />";
			}
			$field_name_check = strtolower($data2["name"]);
			if ($field_name_check=="subject") {
				$aux_subject = " - ".$data2["value"];
			} elseif (!isset($aux_subject)) {
			        // alternative names for subject field:
				if (($field_name_check=="summary") ||
						($field_name_check=="name") ||
						($field_name_check=="title") ||
						($field_name_check=="topic")) {
						$aux_subject = " - ".$data2["value"];
				} elseif ($data2["type"] == 't' && !isset($first_text_field)) {
					$first_text_field = " - ".$data2["name"].": ".$data2["value"];
				}
			}	
		}
	}
	if (!isset($aux_subject) && isset($first_text_field))
	  $data[$titleId] .= $first_text_field;
	elseif (isset($aux_subject))
	  $data[$titleId] .= $aux_subject;

	$data["id"]=$_REQUEST["trackerId"];
	$data["field_values"]=null;

	$changes["data"][] = $data;
	$data=null;
}
$tmp=null;
$changes["data"] = array_reverse($changes["data"]);

$output = $rsslib->generate_feed($feed, $uniqueid, '', $changes, $readrepl, $urlparam, $id, $title, $titleId, $desc, $descId, $dateId, $authorId);
$changes=null;

header("Content-type: ".$output["content-type"]);
print $output["data"];

?>
