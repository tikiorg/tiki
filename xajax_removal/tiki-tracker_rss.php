<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');
require_once ('lib/trackers/trackerlib.php');
require_once ('lib/rss/rsslib.php');
if ($prefs['feed_tracker'] != 'y') {
	$errmsg = tra("rss feed disabled");
	require_once ('tiki-rss_error.php');
}
if ($prefs['feature_trackers'] != 'y') {
	$errmsg = tra("This feature is disabled") . ": feature_trackers";
	require_once ('tiki-rss_error.php');
}
if (!isset($_REQUEST["trackerId"])) {
	$errmsg = tra("No trackerId specified");
	require_once ('tiki-rss_error.php');
}
$perms = Perms::get(array('type' => 'tracker', 'object' => $_REQUEST['trackerId']));
if ($tiki_p_admin_trackers != 'y' && (!$perms->view_trackers && !$perms->view_trackers_pending && !$perms->view_trackers_closed)) {
	$smarty->assign('errortype', 401);
	$errmsg = tra("You do not have permission to view this section");
	require_once ('tiki-rss_error.php');
}
$feed = "tracker";
$id = "trackerId";
$uniqueid = "$feed.id=" . $_REQUEST["trackerId"];
if (isset($_REQUEST['sort_mode'])) {
	$sort_mode = $_REQUEST['sort_mode'];
	$uniqueid.= $sort_mode;
} else {
	$sort_mode = 'created_desc';
}
$output = $rsslib->get_from_cache($uniqueid);
if ($output["data"] == "EMPTY") {
	$tmp = $tikilib->get_tracker($_REQUEST["$id"]);
	if (empty($tmp)) {
		$errmsg = tra("Incorrect param");
		require_once ('tiki-rss_error.php');
	}
	$title = $prefs['feed_tracker_title'] . $tmp["name"];
	$desc = $prefs['feed_tracker_desc'] . $tmp["description"];
	$tmp = null;
	$tmp = $prefs['feed_' . $feed . '_title'];
	if ($tmp <> '') $title = $tmp;
	$tmp = $prefs['feed_' . $feed . '_desc'];
	if ($desc <> '') $desc = $tmp;
	$titleId = "rss_subject";
	$descId = "rss_description";
	$authorId = ""; // "user";
	$dateId = "created";
	$urlparam = "itemId";
	$readrepl = "tiki-view_tracker_item.php?$id=%s&$urlparam=%s";
	$listfields = $trklib->list_tracker_fields($_REQUEST[$id]);
	$fields = array();
	foreach($listfields['data'] as $f) {
		if ($f['isHidden'] == 'y' || $f['isHidden'] == 'c') continue;
		$fields[$f['fieldId']] = $f;
	}
	if (isset($_REQUEST['filterfield'])) {
		$filterfield = explode(':', $_REQUEST['filterfield']);
		if (isset($_REQUEST['exactvalue'])) {
			$exactvalue = explode(':', $_REQUEST['exactvalue']);
		}
		if (isset($_REQUEST['filtertvalue'])) {
			$exactvalue = explode(':', $_REQUEST['filtervalue']);
		}
	} else {
		$filterfield = null;
		$exactvalue = null;
		$filtervalue = null;
	}
	if (isset($_REQUEST['status'])) {
		if (!$trklib->valid_status($_REQUEST['status'])) {
			$errmsg = tra("Incorrect parameter");
			require_once ('tiki-rss_error.php');
		}
		$status = $_REQUEST['status'];
	} else {
		$status = 'opc';
	}
	if (!$perms->view_trackers) {
		$status = str_replace('o', '', $status);
	}
	if (!$perms->view_trackers_pending) {
		$status = str_replace('p', '', $status);
	}
	if (!$perms->view_trackers_closed) {
		$status = str_replace('c', '', $status);
	}
	if (empty($status)) {
		$smarty->assign('errortype', 401);
		$errmsg = tra("You do not have permission to view this section");
		require_once ('tiki-rss_error.php');
	}
	$tmp = $trklib->list_items($_REQUEST[$id], 0, $prefs['feed_tracker_max'], $sort_mode, $fields, $filterfield, $filtervalue, $status, null, $exactvalue);
	foreach($tmp["data"] as $data) {
		$data[$titleId] = (isset($_REQUEST['showitemId']) && $_REQUEST['showitemId'] == 'n')? '': tra('Tracker item:') . ' #' . $data[$urlparam];
		$data[$descId] = '';
		$first_text_field = null;
		$aux_subject = null;
		foreach($data["field_values"] as $data2) {
			if (isset($data2["name"])) {
				$smarty->assign_by_ref('field_value', $data2);
				$smarty->assign_by_ref('item', $data);
				$data2['value'] = $smarty->fetch('tracker_item_field_value.tpl');
				if ($data2['value'] == '') {
					$data2['value'] = '(' . tra('empty') . ')';
				} else {
					$data2['value'] = htmlspecialchars_decode($data2['value']);
				}
				$data[$descId].= $data2["name"] . ": " . $data2["value"] . "<br />";
				$field_name_check = strtolower($data2["name"]);
				if ($field_name_check == "subject") {
					$aux_subject = " - " . $data2["value"];
				} elseif (!isset($aux_subject)) {
					// alternative names for subject field:
					if (($field_name_check == "summary") || ($field_name_check == "name") || ($field_name_check == "title") || ($field_name_check == "topic")) {
						$aux_subject = $data2["value"];
					} elseif ($data2["type"] == 't' && !isset($first_text_field)) {
						$first_text_field = $data2["name"] . ": " . $data2["value"];
					}
				}
			}
		}
		if (isset($_REQUEST['noId']) && $_REQUEST['noId'] == 'y') {
			$data[$titleId] = empty($aux_subject) ? $first_text_field : $aux_subject;
		} elseif (!isset($aux_subject) && isset($first_text_field)) {
			$data[$titleId] .= (empty($data[$titleId])?'': ' - ') . $first_text_field;
		} elseif (isset($aux_subject)) {
			$data[$titleId] .= (empty($data[$titleId])?'': ' - ') . $aux_subject;
		}
		$data["id"] = $_REQUEST["$id"];
		$data["field_values"] = null;
		$changes["data"][] = $data;
		$data = null;
	}
	$tmp = null;
	if (isset($changes['data'])) {
		$output = $rsslib->generate_feed($feed, $uniqueid, '', $changes, $readrepl, $urlparam, $id, $title, $titleId, $desc, $descId, $dateId, $authorId);
	}
	$changes = null;
}
header("Content-type: " . $output["content-type"]);
print $output["data"];
