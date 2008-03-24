<?php

// $Header: /cvsroot/tikiwiki/tiki/messu-sent.php,v 1.6 2007-10-12 07:55:23 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

include_once ('lib/messu/messulib.php');

if (!$user) {
	if ($prefs['feature_redirect_on_error'] == 'y') {
		header('location: '.$prefs['tikiIndex']);
		die;
	} else {
	$smarty->assign('msg', tra("You are not logged in"));
	$smarty->display("error.tpl");
	die;
	}
}

if ($prefs['feature_messages'] != 'y') {
	if ($prefs['feature_redirect_on_error'] == 'y') {
		header('location: '.$prefs['tikiIndex']);
		die;
	} else {
	$smarty->assign('msg', tra("This feature is disabled").": feature_messages");
	$smarty->display("error.tpl");
	die;
	}
}

if ($tiki_p_messages != 'y') {
	$smarty->assign('msg', tra("Permission denied"));
	$smarty->display("error.tpl");
	die;
}

$maxRecords = $messulib->get_user_preference($user, 'maxRecords', 20);

// Delete messages if the delete button was pressed
if (isset($_REQUEST["delete"]) && isset($_REQUEST["msg"])) {
	check_ticket('messu-sent');
	foreach (array_keys($_REQUEST["msg"])as $msg) {
		$messulib->delete_message($user, $msg, 'sent');
	}
}

// Archive messages if the archive button was pressed
if (isset($_REQUEST["archive"]) && isset($_REQUEST["msg"])) {
	check_ticket('messu-sent');
	$tmp = $messulib->count_messages($user, 'archive');
	foreach (array_keys($_REQUEST["msg"])as $msg) {
		if  (($prefs['messu_archive_size']>0) && ($tmp>=$prefs['messu_archive_size'])) {
			$smarty->assign('msg', tra("Archive is full. Delete some messages from archive first."));
			$smarty->display("error.tpl");
			die;
		}
		$messulib->archive_message($user, $msg, 'sent');
		$tmp++;
	}
}

// Download messages if the download button was pressed
if (isset($_REQUEST["download"])) {
	check_ticket('messu-sent');
	// if message ids are handed over, use them:
	if (isset($_REQUEST["msg"])) {
		foreach (array_keys($_REQUEST["msg"])as $msg) {
			$tmp = $messulib->get_message($user, $msg, 'sent');
			$items[] = $tmp;
		}
	} else {
			$items = $messulib->get_messages($user, 'sent', '', '', '');
	}
	$smarty->assign_by_ref('items', $items);

	header("Content-type: application/download ");
    header("Content-Disposition: attachment; filename=tiki-msg-sent-".time("U").".txt ");
	$smarty->display("messu-download.tpl");
	die;
}

if (isset($_REQUEST['filter'])) {
	if ($_REQUEST['flags'] != '') {
		$parts = explode('_', $_REQUEST['flags']);

		$_REQUEST['flag'] = $parts[0];
		$_REQUEST['flagval'] = $parts[1];
	}
}

if (!isset($_REQUEST["priority"]))
	$_REQUEST["priority"] = '';

if (!isset($_REQUEST["flag"]))
	$_REQUEST["flag"] = '';

if (!isset($_REQUEST["flagval"]))
	$_REQUEST["flagval"] = '';

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
$items = $messulib->list_user_messages($user, $offset, $maxRecords, $sort_mode,
	$find, $_REQUEST["flag"], $_REQUEST["flagval"], $_REQUEST['priority'], 'sent');

$cant_pages = ceil($items["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($items["cant"] > ($offset + $maxRecords)) {
	$smarty->assign('next_offset', $offset + $maxRecords);
} else {
	$smarty->assign('next_offset', -1);
}

if ($offset > 0) {
	$smarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$smarty->assign('prev_offset', -1);
}

$smarty->assign_by_ref('items', $items["data"]);

$cellsize = 200;
$percentage = 1;
if ($prefs['messu_sent_size']>0) {
	$current_number = $messulib->count_messages($user,'sent');
	$smarty->assign('messu_sent_number', $current_number);
	$smarty->assign('messu_sent_size', $prefs['messu_sent_size']);
	$percentage = ($current_number / $prefs['messu_sent_size']) * 100;
	$cellsize = round($percentage / 100 * 200);
	if ($current_number>$prefs['messu_sent_size']) $cellsize=200;
	if ($cellsize<1) $cellsize=1;
	$percentage = round($percentage);
}
$smarty->assign('cellsize', $cellsize);
$smarty->assign('percentage', $percentage);

$section = 'user_messages';
include_once ('tiki-section_options.php');

include_once ('tiki-mytiki_shared.php');
ask_ticket('messu-sent');

$smarty->assign('mid', 'messu-sent.tpl');
$smarty->display("tiki.tpl");

?>
