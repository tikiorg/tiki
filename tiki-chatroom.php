<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-chatroom.php,v 1.8 2003-11-17 15:44:28 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/chat/chatlib.php');

if ($feature_chat != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_chat");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_chat != 'y') {
	$smarty->assign('msg', tra("Permission denied to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["channelId"])) {
	$smarty->assign('msg', tra("No channel indicated"));

	$smarty->display("error.tpl");
	die;
}

$channelId = $_REQUEST["channelId"];
//session_register('channelId');
if ($user) {
	$nickname = $user;
} else {
	if (!isset($_REQUEST["nickname"]) || empty($_REQUEST["nickname"])) {
		$smarty->assign('msg', tra("No nickname indicated"));

		$smarty->display("error.tpl");
		die;
	}

	$nickname = $_REQUEST["nickname"];
}

//session_register("nickname");
$enterTime = date("U");

//session_register('enterTime');
if ($tiki_p_admin_chat == 'y') {
	$nickname = '@' . $nickname;
}

$chatlib->user_to_channel($nickname, $channelId);
$smarty->assign('nickname', $nickname);
$smarty->assign('channelId', $_REQUEST["channelId"]);
$smarty->assign('now', date("U"));
$info = $chatlib->get_channel($_REQUEST["channelId"]);
$refresh = $info["refresh"];
$name = $info["name"];
//session_register('refresh');
$smarty->assign('channelName', $name);
$smarty->assign_by_ref('channelInfo', $info);
$smarty->assign('refresh', $refresh);
$channels = $chatlib->list_active_channels(0, -1, 'name_desc', '');
$smarty->assign_by_ref('channels', $channels["data"]);
$chatusers = $chatlib->get_chat_users($channelId);
$smarty->assign_by_ref('chatusers', $chatusers);

$section = 'chat';
include_once ('tiki-section_options.php');

// Display the template
$smarty->assign('mid', 'tiki-chatroom.tpl');
$smarty->display("tiki.tpl");

?>
