<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-newsreader_servers.php,v 1.13 2003-12-28 20:12:52 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

include_once ('lib/newsreader/newslib.php');

if (!$user) {
	$smarty->assign('msg', tra("You are not logged in"));

	$smarty->display("error.tpl");
	die;
}

if ($feature_newsreader != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_newsreader");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_newsreader != 'y') {
	$smarty->assign('msg', tra("Permission denied to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["serverId"]))
	$_REQUEST["serverId"] = 0;

if (isset($_REQUEST["remove"])) {
	check_ticket('news-server');
	$newslib->remove_server($user, $_REQUEST['remove']);
}

if ($_REQUEST["serverId"]) {
	$info = $newslib->get_server($user, $_REQUEST["serverId"]);
} else {
	$info = array();

	$info['server'] = '';
	$info['port'] = 119;
	$info['username'] = '';
	;
	$info['password'] = '';
}

if (isset($_REQUEST['save'])) {
	check_ticket('news-server');
	$newslib->replace_server(
		$user, $_REQUEST["serverId"], $_REQUEST["server"], $_REQUEST["port"], $_REQUEST['username'], $_REQUEST['password']);

	$info = array();
	$info['server'] = '';
	$info['port'] = 119;
	$info['username'] = '';
	$info['password'] = '';
	$_REQUEST["serverId"] = 0;
}

$smarty->assign('serverId', $_REQUEST["serverId"]);
$smarty->assign('info', $info);

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'serverId_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}

if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}

$smarty->assign_by_ref('offset', $offset);

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$smarty->assign('find', $find);

$smarty->assign_by_ref('sort_mode', $sort_mode);

if (isset($_SESSION['thedate'])) {
	$pdate = $_SESSION['thedate'];
} else {
	$pdate = date("U");
}

$channels = $newslib->list_servers($user, $offset, $maxRecords, $sort_mode, $find);
$cant_pages = ceil($channels["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($channels["cant"] > ($offset + $maxRecords)) {
	$smarty->assign('next_offset', $offset + $maxRecords);
} else {
	$smarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($offset > 0) {
	$smarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$smarty->assign('prev_offset', -1);
}

$smarty->assign_by_ref('channels', $channels["data"]);

$section = 'newsreader';
include_once ('tiki-section_options.php');

include_once ('tiki-mytiki_shared.php');
ask_ticket('news-server');

$smarty->assign('mid', 'tiki-newsreader_servers.tpl');
$smarty->display("tiki.tpl");

?>
