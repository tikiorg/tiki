<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-chat_users.php,v 1.12 2005-01-01 00:16:32 damosoft Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

$section = 'chat';
require_once ("tiki-setup.php");

include_once ('lib/chat/chatlib.php');

if (isset($_REQUEST['channelId'])) {
    $chatusers = $chatlib->get_chat_users($_REQUEST["channelId"]);
} else {
    $chatusers = array();
}

$smarty->assign('chatusers',$chatusers);

$smarty->display('tiki-chat_users.tpl');

?>
