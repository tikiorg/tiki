<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-chat_box.php,v 1.6 2003-12-28 20:12:51 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
include_once ('tiki-setup.php');

include_once ('lib/chat/chatlib.php');

if ($feature_chat != 'y') {
	die;
}

if ($tiki_p_chat != 'y') {
	die;
}

// Check if "send" is set (the user is sending a message)
// check if the channel is moderated if moderated save to moderated messages
// else write the message into messages
// use send_message(userId,channelId,data)
if (isset($_REQUEST["channelId"]) && isset($_REQUEST["nickname"])) {
	check_ticket('chat');
	if (isset($_REQUEST["data"])) {
		$data = $_REQUEST["data"];

		// Recognize a private message
		if (substr($data, 0, 1) == ':') {
			preg_match("/:([^:]+):(.*)/", $data, $reqs);

			$chatlib->send_private_message($_REQUEST["nickname"], $reqs[1], $reqs[2]);
		} else {
			if ($_REQUEST["channelId"]) {
				$chatlib->send_message($_REQUEST["nickname"], $_REQUEST["channelId"], $data);
			}
		}
	}

	$smarty->assign('nickname', $_REQUEST["nickname"]);
	$smarty->assign('channelId', $_REQUEST["channelId"]);
}

// Displaythe box if we are in an active channel
if (isset($_REQUEST["channelId"])) {
	$smarty->display('tiki-chat_box.tpl');
// If not display a message
} else {
	print ("no channel selected");
}

?>
