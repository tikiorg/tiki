<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_poll_options.php,v 1.12 2004-03-28 07:32:23 mose Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

require_once ('lib/tikilib.php'); # httpScheme()
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
	$smarty->assign('msg', tra("No poll indicated"));

	$smarty->display("error.tpl");
	die;
}

$smarty->assign('pollId', $_REQUEST["pollId"]);
$menu_info = $tikilib->get_poll($_REQUEST["pollId"]);
$smarty->assign('menu_info', $menu_info);

if (!isset($_REQUEST["optionId"])) {
	$_REQUEST["optionId"] = 0;
}

$smarty->assign('optionId', $_REQUEST["optionId"]);

if ($_REQUEST["optionId"]) {
	$info = $polllib->get_poll_option($_REQUEST["optionId"]);
} else {
	$info = array();

	$info["title"] = '';
	$info["votes"] = 0;
}

$smarty->assign('title', $info["title"]);
$smarty->assign('votes', $info["votes"]);

if (isset($_REQUEST["remove"])) {
	check_ticket('admin-poll-options');
	$polllib->remove_poll_option($_REQUEST["remove"]);
}

if (isset($_REQUEST["save"])) {
	check_ticket('admin-poll-options');
	$polllib->replace_poll_option($_REQUEST["pollId"], $_REQUEST["optionId"], $_REQUEST["title"]);
}

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'pollId_asc';
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
$channels = $polllib->list_poll_options($_REQUEST["pollId"], 0, -1, $sort_mode, $find);
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

$smarty->assign('ownurl', httpPrefix(). $_SERVER["REQUEST_URI"]);
$smarty->assign_by_ref('channels', $channels["data"]);
ask_ticket('admin-poll-options');

// Display the template
$smarty->assign('mid', 'tiki-admin_poll_options.tpl');
$smarty->display("tiki.tpl");

?>
