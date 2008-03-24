<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-newsreader_groups.php,v 1.18 2007-10-12 07:55:29 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
$section = 'newsreader';
require_once ('tiki-setup.php');

include_once ('lib/newsreader/newslib.php');

if (!$user) {
	$smarty->assign('msg', tra("You are not logged in"));

	$smarty->display("error.tpl");
	die;
}

if ($prefs['feature_newsreader'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_newsreader");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_newsreader != 'y') {
	$smarty->assign('msg', tra("Permission denied to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["serverId"])) {
	$smarty->assign('msg', tra("No server indicated"));

	$smarty->display("error.tpl");
	die;
}

if ($_REQUEST["serverId"]) {
	$info = $newslib->get_server($user, $_REQUEST["serverId"]);
}

$smarty->assign('serverId', $_REQUEST["serverId"]);
$smarty->assign('info', $info);

if (!$newslib->news_set_server($info['server'], $info['port'], $info['username'], $info['password'])) {
	$smarty->assign('msg', tra("Cannot connect to"). ':' . $info['server']);

	$smarty->display("error.tpl");
	die;
}

$groups = $newslib->news_get_groups();
$smarty->assign_by_ref('groups', $groups);
//print_r($groups);
include_once ('tiki-mytiki_shared.php');

include_once ('tiki-section_options.php');
ask_ticket('news-groups');

$smarty->assign('mid', 'tiki-newsreader_groups.tpl');
$smarty->display("tiki.tpl");

?>
