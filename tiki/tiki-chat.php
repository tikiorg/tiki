<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-chat.php,v 1.9 2004-06-06 08:42:45 damosoft Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'chat';
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

$channels = $chatlib->list_active_channels(0, -1, 'name_desc', '');
$smarty->assign('channels', $channels["data"]);

include_once ('tiki-section_options.php');

// Display the template
$smarty->assign('mid', 'tiki-chat.tpl');
$smarty->display("tiki.tpl");

?>
