<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_polls.php,v 1.14 2004-10-15 15:54:42 damosoft Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/polls/polllib.php');

if (!isset($polllib)) {
	$polllib = new PollLib($dbTiki);
}

if ($tiki_p_admin != 'y') {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));

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
	$info["description"] = '';
	$info["active"] = 'y';
	$info["publishDate"] = date("U");
	$info["releaseDate"] = date("U")+60*60*24*14;
}

$smarty->assign('title', $info["title"]);
$smarty->assign('description', $info["description"]);
$smarty->assign('active', $info["active"]);
$smarty->assign('publishDate', $info["publishDate"]);
$smarty->assign('releaseDate', $info["releaseDate"]);

if (isset($_REQUEST["remove"])) {
	$area = 'delpoll';
	if ($feature_ticketlib2 != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
		key_check($area);
		$polllib->remove_poll($_REQUEST["remove"]);
	} else {
		key_get($area);
	}
}

if (isset($_REQUEST["save"])) {
	check_ticket('admin-polls');
	$publishDate = mktime($_REQUEST["Time_Hour"], $_REQUEST["Time_Minute"], 0, $_REQUEST["Date_Month"], $_REQUEST["Date_Day"], $_REQUEST["Date_Year"]);
	$releaseDate = mktime($_REQUEST["r_Time_Hour"], $_REQUEST["r_Time_Minute"], 0, $_REQUEST["r_Date_Month"], $_REQUEST["r_Date_Day"], $_REQUEST["r_Date_Year"]);

	$pid = $polllib->replace_poll($_REQUEST["pollId"], $_REQUEST["title"], $_REQUEST['description'],$_REQUEST["active"], $publishDate, $releaseDate);

	$cat_type = 'poll';
	$cat_objid = $pid;
	$cat_desc = substr($_REQUEST["description"], 0, 200);
	$cat_name = $_REQUEST["title"];
	$cat_href = "tiki-poll_results.php?pollId=" . $cat_objid;
	include_once ("categorize.php");
}

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'releaseDate_desc';
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

$smarty->assign_by_ref('channels', $channels["data"]);

$cat_type = 'poll';
$cat_objid = $_REQUEST["pollId"];
include_once ("categorize_list.php");
ask_ticket('admin-polls');

// Display the template
$smarty->assign('mid', 'tiki-admin_polls.tpl');
$smarty->display("tiki.tpl");

?>
