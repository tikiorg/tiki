<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_trackers.php,v 1.12 2004-01-22 07:55:52 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/trackers/trackerlib.php');

if ($feature_trackers != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_trackers");
	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_admin_trackers != 'y') {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["trackerId"])) {
	$_REQUEST["trackerId"] = 0;
}

$smarty->assign('trackerId', $_REQUEST["trackerId"]);

$smarty->assign('individual', 'n');

if ($userlib->object_has_one_permission($_REQUEST["trackerId"], 'tracker')) {
	$smarty->assign('individual', 'y');
}

$info = array();
$info["name"] = '';
$info["description"] = '';
$info["showCreated"] = '';
$info["showStatus"] = '';
$info["showStatusAdminOnly"] = '';
$info["showLastModif"] = '';
$info["useComments"] = '';
$info["useAttachments"] = '';
$info["showComments"] = '';
$info["showAttachments"] = '';
$info["orderAttachments"] = 'name,created,filesize,downloads,desc';

if ($_REQUEST["trackerId"]) {
	$info = array_merge($info,$tikilib->get_tracker($_REQUEST["trackerId"]));
	$info = array_merge($info,$trklib->get_tracker_options($_REQUEST["trackerId"]));
} 

$smarty->assign('name', $info["name"]);
$smarty->assign('description', $info["description"]);
$smarty->assign('showCreated', $info["showCreated"]);
$smarty->assign('showStatus', $info["showStatus"]);
$smarty->assign('showStatusAdminOnly', $info["showStatusAdminOnly"]);
$smarty->assign('showLastModif', $info["showLastModif"]);
$smarty->assign('useComments', $info["useComments"]);
$smarty->assign('useAttachments', $info["useAttachments"]);
$smarty->assign('showComments', $info["showComments"]);
$smarty->assign('showAttachments', $info["showAttachments"]);

$outatt = array();
$info["orderPopup"] = '';
if (strstr($info["orderAttachments"],'|')) {
	$part = split("\|",$info["orderAttachments"]);
	$info["orderAttachments"] = $part[0];
	$info["orderPopup"] = $part[1];
}
$i = 1;
foreach (split(',',$info["orderAttachments"]) as $it) {
	$outatt["$it"] = $i;
	$i++;
}
$i = -1;
foreach (split(',',$info["orderPopup"]) as $it) {
	$outatt["$it"] = $i;
	$i--;
}
$smarty->assign('ui', $outatt);

if (isset($_REQUEST["remove"])) {
	check_ticket('admin-trackers');
	$trklib->remove_tracker($_REQUEST["remove"]);
}

if (isset($_REQUEST["save"])) {
	check_ticket('admin-trackers');
	if (isset($_REQUEST["showCreated"]) && $_REQUEST["showCreated"] == 'on') {
		$tracker_options["showCreated"] = 'y';
	}

	if (isset($_REQUEST["showStatus"]) && $_REQUEST["showStatus"] == 'on') {
		$tracker_options["showStatus"] = 'y';
	}

	if (isset($_REQUEST["showStatusAdminOnly"]) && $_REQUEST["showStatusAdminOnly"] == 'on') {
		$tracker_options["showStatusAdminOnly"] = 'y';
	}

	if (isset($_REQUEST["useComments"]) && $_REQUEST["useComments"] == 'on') {
		$tracker_options["useComments"] = 'y';
	}

	if (isset($_REQUEST["useAttachments"]) && $_REQUEST["useAttachments"] == 'on') {
		$tracker_options["useAttachments"] = 'y';
	}

	if (isset($_REQUEST["showComments"]) && $_REQUEST["showComments"] == 'on') {
		$tracker_options["showComments"] = 'y';
	}

	if (isset($_REQUEST["showAttachments"]) && $_REQUEST["showAttachments"] == 'on') {
		$tracker_options["showAttachments"] = 'y';
	}

	if (isset($_REQUEST["showLastModif"]) && $_REQUEST["showLastModif"] == 'on') {
		$tracker_options["showLastModif"] = 'y';
	}

	if (isset($_REQUEST['ui']) and is_array($_REQUEST['ui'])) {
		$showlist = array();
		$popupinfo = array();
		foreach ($_REQUEST['ui'] as $kk=>$vv) {
			if ($vv > 0) { $showlist[$vv] = $kk; }
			if ($vv < 0) { $popupinfo[$vv] = $kk; }
		}
		ksort($showlist);
		krsort($popupinfo);
		$orderat = implode(',',$showlist);
		if (count($popupinfo)) {
			$orderat.= '|'.implode(',',$popupinfo);
		}
		$tracker_options[" orderAttachments"] = $orderat;
	}

	$trklib->replace_tracker($_REQUEST["trackerId"], $_REQUEST["name"], $_REQUEST["description"], $showCreated, $showLastModif, $tracker_options);
	$smarty->assign('trackerId', 0);
	$smarty->assign('name', '');
	$smarty->assign('description', '');
	$smarty->assign('ui',array());
}

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'created_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}

if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}

$smarty->assign_by_ref('offset', $offset);

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$smarty->assign('find', $find);

$smarty->assign_by_ref('sort_mode', $sort_mode);
$channels = $trklib->list_trackers($offset, $maxRecords, $sort_mode, $find);

for ($i = 0; $i < count($channels["data"]); $i++) {
	if ($userlib->object_has_one_permission($channels["data"][$i]["trackerId"], 'tracker')) {
		$channels["data"][$i]["individual"] = 'y';
	} else {
		$channels["data"][$i]["individual"] = 'n';
	}
}

$cant_pages = ceil($channels["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($channels["cant"] > ($offset + $maxRecords)) {
	$smarty->assign('next_offset', $offset + $maxRecords);
} else {
	$smarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($offset > 0) {
	$smarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$smarty->assign('prev_offset', -1);
}

$smarty->assign_by_ref('channels', $channels["data"]);

$smarty->assign('uses_tabs', 'y');

// block for categorization
$cat_type = 'tracker';
$cat_objid = $_REQUEST["trackerId"];
include_once ("categorize_list.php");
ask_ticket('admin-trackers');

// Display the template
$smarty->assign('mid', 'tiki-admin_trackers.tpl');
$smarty->display("tiki.tpl");

?>
