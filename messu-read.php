<?php

// $Header: /cvsroot/tikiwiki/tiki/messu-read.php,v 1.10 2003-10-08 03:53:08 dheltzel Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

include_once ('lib/messu/messulib.php');

if (!$user) {
	$smarty->assign('msg', tra("You are not logged in"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if ($feature_messages != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_messages");

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if ($tiki_p_messages != 'y') {
	$smarty->assign('msg', tra("Permission denied"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if (isset($_REQUEST["delete"])) {
	$messulib->delete_message($_SESSION['user'], $_REQUEST['msgdel']);
}

$smarty->assign('sort_mode', $_REQUEST['sort_mode']);
$smarty->assign('find', $_REQUEST['find']);
$smarty->assign('flag', $_REQUEST['flag']);
$smarty->assign('offset', $_REQUEST['offset']);
$smarty->assign('flagval', $_REQUEST['flagval']);
$smarty->assign('priority', $_REQUEST['priority']);
$smarty->assign('legend', '');

if (!isset($_REQUEST['msgId']) || $_REQUEST['msgId'] == 0) {
	$smarty->assign('legend', tra("No more messages"));

	$smarty->assign('mid', 'messu-read.tpl');
	$smarty->display("styles/$style_base/tiki.tpl");
	die;
}

if (isset($_REQUEST['action'])) {
	$messulib->flag_message($_SESSION['user'], $_REQUEST['msgId'], $_REQUEST['action'], $_REQUEST['actionval']);
}

// Using the sort_mode, flag, flagval and find get the next and prev messages
$smarty->assign('msgId', $_REQUEST['msgId']);
$next = $messulib->get_next_message($_SESSION['user'], $_REQUEST['msgId'], $_REQUEST['sort_mode'], $_REQUEST['find'],
	$_REQUEST['flag'], $_REQUEST['flagval'], $_REQUEST['priority']);
$prev = $messulib->get_prev_message($_SESSION['user'], $_REQUEST['msgId'], $_REQUEST['sort_mode'], $_REQUEST['find'],
	$_REQUEST['flag'], $_REQUEST['flagval'], $_REQUEST['priority']);
$smarty->assign('next', $next);
$smarty->assign('prev', $prev);

// Mark the message as read
$messulib->flag_message($_SESSION['user'], $_REQUEST['msgId'], 'isRead', 'y');

// Get the message and assign its data to template vars
$msg = $messulib->get_message($_SESSION['user'], $_REQUEST['msgId']);
$smarty->assign('msg', $msg);

$section = 'user_messages';
include_once ('tiki-section_options.php');
include_once ('tiki-mytiki_shared.php');
$smarty->assign('mid', 'messu-read.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>
