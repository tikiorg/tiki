<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_poll_options.php,v 1.23 2007-10-12 07:55:24 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

require_once ('lib/tikilib.php'); # httpScheme()
include_once ('lib/polls/polllib.php');

if (!isset($polllib)) {
	$polllib = new PollLib($dbTiki);
}

if ($tiki_p_admin_polls != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["pollId"])) {
	$smarty->assign('msg', tra("No poll indicated"));

	$smarty->display("error.tpl");
	die;
}

$smarty->assign('pollId', $_REQUEST["pollId"]);
$menu_info = $polllib->get_poll($_REQUEST["pollId"]);
$smarty->assign('menu_info', $menu_info);

if (!isset($_REQUEST["optionId"])) {
	$_REQUEST["optionId"] = 0;
}

if (isset($_REQUEST["remove"])) {
	$area = 'delpolloption';
	if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
		key_check($area);
		$polllib->remove_poll_option($_REQUEST["remove"]);
	} else {
		key_get($area);
	}
}

if (isset($_REQUEST["save"])) {
	check_ticket('admin-poll-options');
	$polllib->replace_poll_option($_REQUEST["pollId"], $_REQUEST["optionId"], $_REQUEST["title"], $_REQUEST['position']);
	$_REQUEST["optionId"] = 0;
}

$smarty->assign('optionId', $_REQUEST["optionId"]);

if ($_REQUEST["optionId"]) {
	$info = $polllib->get_poll_option($_REQUEST["optionId"]);
} else {
	$info = array();
	$info["title"] = '';
	$info["votes"] = 0;
	$info["position"] = '';
}

$smarty->assign('title', $info["title"]);
$smarty->assign('votes', $info["votes"]);

$channels = $polllib->list_poll_options($_REQUEST["pollId"]);
$smarty->assign('ownurl', $tikilib->httpPrefix(). $_SERVER["REQUEST_URI"]);
$smarty->assign_by_ref('channels', $channels);

ask_ticket('admin-poll-options');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-admin_poll_options.tpl');
$smarty->display("tiki.tpl");

?>
