<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-poll_form.php,v 1.9 2003-11-17 15:44:29 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

require_once ('lib/tikilib.php'); # httpScheme()
include_once ('lib/polls/polllib.php');

if (!isset($polllib)) {
	$polllib = new PollLib($dbTiki);
}

if ($feature_polls != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_polls");

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["pollId"])) {
	$smarty->assign('msg', tra("No poll indicated"));

	$smarty->display("error.tpl");
	die;
}

$poll_info = $tikilib->get_poll($_REQUEST["pollId"]);
//$polls = $polllib->list_active_polls(0,-1,'publishDate_desc','');
$options = $polllib->list_poll_options($_REQUEST["pollId"], 0, -1, 'title_desc', '');

$smarty->assign_by_ref('menu_info', $poll_info);
//$smarty->assign_by_ref('polls',$polls["data"]);
$smarty->assign_by_ref('channels', $options["data"]);
$smarty->assign('ownurl', httpPrefix(). $_SERVER["REQUEST_URI"]);

// Display the template
$smarty->assign('mid', 'tiki-poll_form.tpl');
$smarty->display("tiki.tpl");

?>
