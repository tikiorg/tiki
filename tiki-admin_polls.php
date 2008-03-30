<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_polls.php,v 1.26.2.1 2007-12-06 13:46:07 nkoth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/polls/polllib.php');

if (!isset($polllib)) {
	$polllib = new PollLib($dbTiki);
}

if ($prefs['feature_polls'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_polls");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_admin_polls != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["pollId"])) {
	$_REQUEST["pollId"] = 0;
}

$smarty->assign('pollId', $_REQUEST["pollId"]);

if (isset($_REQUEST["setlast"])) {
	check_ticket('admin-polls');
	$polllib->set_last_poll();
}

if (isset($_REQUEST["closeall"])) {
	check_ticket('admin-polls');
	$polllib->close_all_polls();
}

if (isset($_REQUEST["activeall"])) {
	check_ticket('admin-polls');
	$polllib->active_all_polls();
}

if ($_REQUEST["pollId"]) {
	$info = $polllib->get_poll($_REQUEST["pollId"]);
} else {
	$info = array();

	$info["title"] = '';
	$info["active"] = 'y';
	$info["publishDate"] = $tikilib->now;
}

$smarty->assign('title', $info["title"]);
$smarty->assign('active', $info["active"]);
$smarty->assign('publishDate', $info["publishDate"]);

if (isset($_REQUEST["remove"])) {
	$area = 'delpoll';
	if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
		key_check($area);
		$polllib->remove_poll($_REQUEST["remove"]);
	} else {
		key_get($area);
	}
}

if (isset($_REQUEST["save"])) {
	check_ticket('admin-polls');
	$publishDate = mktime($_REQUEST["Time_Hour"], $_REQUEST["Time_Minute"],
		0, $_REQUEST["Date_Month"], $_REQUEST["Date_Day"], $_REQUEST["Date_Year"]);

	$pid = $polllib->replace_poll($_REQUEST["pollId"], $_REQUEST["title"], $_REQUEST["active"], $publishDate);

	$cat_type = 'poll';
	$cat_objid = $pid;
	$cat_desc = substr($_REQUEST["title"], 0, 200);
	$cat_name = $_REQUEST["title"];
	$cat_href = "tiki-poll_results.php?pollId=" . $cat_objid;
	include_once ("categorize.php");
}

if (isset($_REQUEST['addPoll']) && !empty($_REQUEST['poll_template']) && !empty($_REQUEST['pages'])) {
	global $wikilib; include_once('lib/wiki/wikilib.php');
	global $categlib; include_once('lib/categories/categlib.php');
	$cat_type = 'wiki page';
	foreach ($_REQUEST['pages'] as $cat_objid) {
		if (!$catObjectId = $categlib->is_categorized($cat_type, $cat_objid)) {
			$info = $tikilib->get_page_info;
			$cat_desc = $info['description'];
			$cat_href = 'tiki-index.php?page='.urlencode($cat_objid);
		}
		include('poll_categorize.php');
		if (isset($_REQUEST['locked']) && $_REQUEST['locked'] == 'on')
			$wikilib->lock_page($cat_objid);
	}
}

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'publishDate_desc';
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
$channels = $polllib->list_polls($offset, $maxRecords, $sort_mode, $find);

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

if ($prefs['poll_list_categories'] == 'y') {
	foreach ($channels['data'] as $key=>$channel) {
		$channels['data'][$key]['categories'] = $polllib->get_poll_categories($channel['pollId']);
	}
}
if ($prefs['poll_list_objects'] == 'y') {
	foreach ($channels['data'] as $key=>$channel) {
		$channels['data'][$key]['objects'] = $polllib->get_poll_objects($channel['pollId']);
	}
}
$smarty->assign_by_ref('channels', $channels["data"]);

$listPages = $tikilib->list_pageNames();
$smarty->assign_by_ref('listPages', $listPages['data']);

$cat_type = 'poll';
$cat_objid = $_REQUEST["pollId"];
include_once ("categorize_list.php");

ask_ticket('admin-polls');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-admin_polls.tpl');
$smarty->display("tiki.tpl");

?>
