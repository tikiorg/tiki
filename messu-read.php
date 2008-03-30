<?php

// $Id: /cvsroot/tikiwiki/tiki/messu-read.php,v 1.26 2007-10-12 07:55:23 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
$section = 'user_messages';
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

if (isset($_REQUEST["delete"])) {
	check_ticket('messu-read');
	$messulib->delete_message($user, $_REQUEST['msgdel']);
}

$smarty->assign('sort_mode', $_REQUEST['sort_mode']);
$smarty->assign('find', $_REQUEST['find']);
$smarty->assign('flag', $_REQUEST['flag']);
$smarty->assign('offset', $_REQUEST['offset']);
$smarty->assign('flagval', $_REQUEST['flagval']);
$smarty->assign('priority', $_REQUEST['priority']);
$smarty->assign('legend', '');

if (!isset($_REQUEST['msgId']) || $_REQUEST['msgId'] == 0) {
	$smarty->assign('unread', 0);
	$smarty->assign('legend', tra("No more messages"));

	$smarty->assign('mid', 'messu-read.tpl');
	$smarty->display("tiki.tpl");
	die;
}

if (isset($_REQUEST['action'])) {
	$messulib->flag_message($user, $_REQUEST['msgId'], $_REQUEST['action'], $_REQUEST['actionval']);
}

// Using the sort_mode, flag, flagval and find get the next and prev messages
$smarty->assign('msgId', $_REQUEST['msgId']);
$next = $messulib->get_next_message($user, $_REQUEST['msgId'], $_REQUEST['sort_mode'], $_REQUEST['find'],
	$_REQUEST['flag'], $_REQUEST['flagval'], $_REQUEST['priority']);
$prev = $messulib->get_prev_message($user, $_REQUEST['msgId'], $_REQUEST['sort_mode'], $_REQUEST['find'],
	$_REQUEST['flag'], $_REQUEST['flagval'], $_REQUEST['priority']);
$smarty->assign('next', $next);
$smarty->assign('prev', $prev);

// Mark the message as read in the receivers mailbox
$messulib->flag_message($user, $_REQUEST['msgId'], 'isRead', 'y');

// Get the message and assign its data to template vars
$msg = $messulib->get_message($user, $_REQUEST['msgId']);
$smarty->assign('msg', $msg);

// which quote format should tiki use?
global $prefs;
if ($prefs['feature_use_quoteplugin'] == 'y') {
	$quote_format = 'fancy';
} else {
	$quote_format = 'simple';
}
$smarty->assign('quote_format',$quote_format);

if ($messulib->get_user_preference($user, 'mess_sendReadStatus', 'n') == 'y') {
	// Mark the message as read in the senders sent box:
	$messulib->flag_message($msg['user_from'], $_REQUEST['msgId'], 'isRead', 'y', 'sent');
}
if ($prefs['feature_actionlog'] == 'y') {
	include_once('lib/logs/logslib.php');
	$logslib->add_action('Viewed', '', 'message');
}


ask_ticket('messu-read');

include_once ('tiki-section_options.php');
include_once ('tiki-mytiki_shared.php');
$smarty->assign('mid', 'messu-read.tpl');
$smarty->display("tiki.tpl");

?>
