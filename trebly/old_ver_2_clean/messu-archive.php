<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'user_messages';
require_once ('tiki-setup.php');
include_once ('lib/messu/messulib.php');
$access->check_user($user);
$access->check_feature('feature_messages');
$access->check_permission('tiki_p_messages');
$maxRecords = $messulib->get_user_preference($user, 'maxRecords', 20);
// Delete messages if the delete button was pressed
if (isset($_REQUEST["delete"]) && isset($_REQUEST["msg"])) {
	check_ticket('messu-archive');
	foreach(array_keys($_REQUEST["msg"]) as $msg) {
		$messulib->delete_message($user, $msg, 'archive');
	}
}
// Download messages if the download button was pressed
if (isset($_REQUEST["download"])) {
	check_ticket('messu-archive');
	// if message ids are handed over, use them:
	if (isset($_REQUEST["msg"])) {
		foreach(array_keys($_REQUEST["msg"]) as $msg) {
			$tmp = $messulib->get_message($user, $msg, 'archive');
			$items[] = $tmp;
		}
	} else {
		$items = $messulib->get_messages($user, 'archive', '', '', '');
	}
	$smarty->assign_by_ref('items', $items);
	header("Content-Disposition: attachment; filename=tiki-msg-archive-" . time("U") . ".txt ");
	$smarty->display('messu-download.tpl', null, null, 'application/download');
	die;
}
if (isset($_REQUEST['filter'])) {
	if ($_REQUEST['flags'] != '') {
		$parts = explode('_', $_REQUEST['flags']);
		$_REQUEST['flag'] = $parts[0];
		$_REQUEST['flagval'] = $parts[1];
	}
}
if (!isset($_REQUEST["priority"])) $_REQUEST["priority"] = '';
if (!isset($_REQUEST["flag"])) $_REQUEST["flag"] = '';
if (!isset($_REQUEST["flagval"])) $_REQUEST["flagval"] = '';
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'date_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}
if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}
if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}
$smarty->assign_by_ref('flag', $_REQUEST['flag']);
$smarty->assign_by_ref('priority', $_REQUEST['priority']);
$smarty->assign_by_ref('flagval', $_REQUEST['flagval']);
$smarty->assign_by_ref('offset', $offset);
$smarty->assign_by_ref('sort_mode', $sort_mode);
$smarty->assign('find', $find);
// What are we paginating: items
$items = $messulib->list_user_messages($user, $offset, $maxRecords, $sort_mode, $find, $_REQUEST["flag"], $_REQUEST["flagval"], $_REQUEST['priority'], 'archive');
$smarty->assign_by_ref('cant_pages', $items["cant"]);
$smarty->assign_by_ref('items', $items["data"]);
$cellsize = 200;
$percentage = 1;
if ($prefs['messu_archive_size'] > 0) {
	$current_number = $messulib->count_messages($user, 'archive');
	$smarty->assign('messu_archive_number', $current_number);
	$smarty->assign('messu_archive_size', $prefs['messu_archive_size']);
	$percentage = ($current_number / $prefs['messu_archive_size']) * 100;
	$cellsize = round($percentage / 100 * 200);
	if ($current_number > $prefs['messu_archive_size']) $cellsize = 200;
	if ($cellsize < 1) $cellsize = 1;
	$percentage = round($percentage);
}
$smarty->assign('cellsize', $cellsize);
$smarty->assign('percentage', $percentage);
include_once ('tiki-section_options.php');
include_once ('tiki-mytiki_shared.php');
ask_ticket('messu-archive');
$smarty->assign('mid', 'messu-archive.tpl');
$smarty->display("tiki.tpl");
