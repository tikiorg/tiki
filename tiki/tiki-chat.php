<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-chat.php,v 1.5 2003-08-07 04:33:57 rossta Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/chat/chatlib.php');

if ($feature_chat != 'y') {
	$smarty->assign('msg', tra("This feature is disabled"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if ($tiki_p_chat != 'y') {
	$smarty->assign('msg', tra("Permission denied to use this feature"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

$channels = $chatlib->list_active_channels(0, -1, 'name_desc', '');
$smarty->assign('channels', $channels["data"]);

$section = 'chat';
include_once ('tiki-section_options.php');

// Display the template
$smarty->assign('mid', 'tiki-chat.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>