<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-usermenu.php,v 1.17 2007-10-12 07:55:32 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
$section = 'mytiki';
require_once ('tiki-setup.php');

include_once ('lib/usermenu/usermenulib.php');

if ($prefs['feature_usermenu'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_usermenu");

	$smarty->display("error.tpl");
	die;
}

if (!$user) {
	$smarty->assign('msg', tra("Must be logged to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_usermenu != 'y') {
	$smarty->assign('msg', tra("Permission denied to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["menuId"]))
	$_REQUEST["menuId"] = 0;

if (isset($_REQUEST["delete"]) && isset($_REQUEST["menu"])) {
	check_ticket('user-menu');
	foreach (array_keys($_REQUEST["menu"])as $men) {
		$usermenulib->remove_usermenu($user, $men);
	}

	if (isset($_SESSION['usermenu']))
		unset ($_SESSION['usermenu']);
}

if (isset($_REQUEST['addbk'])) {
	check_ticket('user-menu');
	$usermenulib->add_bk($user);

	if (isset($_SESSION['usermenu']))
		unset ($_SESSION['usermenu']);
}

if ($_REQUEST["menuId"]) {
	$info = $usermenulib->get_usermenu($user, $_REQUEST["menuId"]);
} else {
	$info = array();

	$info['name'] = '';
	$info['url'] = isset($_REQUEST['url']) ? $_REQUEST['url'] : '';
	$info['mode'] = 'w';
	$info['position'] = $usermenulib->get_max_position($user) + 1;
}

if (isset($_REQUEST['save'])) {
	check_ticket('user-menu');
	$usermenulib->replace_usermenu(
		$user, $_REQUEST["menuId"], $_REQUEST["name"], $_REQUEST["url"], $_REQUEST['position'], $_REQUEST['mode']);

	$info = array();
	$info['name'] = '';
	$info['url'] = '';
	$info['position'] = 1;
	$_REQUEST["menuId"] = 0;
	unset ($_SESSION['usermenu']);
}

$smarty->assign('menuId', $_REQUEST["menuId"]);
$smarty->assign('info', $info);

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'position_asc';
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
	$pdate = $tikilib->now;
}

$channels = $usermenulib->list_usermenus($user, $offset, $maxRecords, $sort_mode, $find);

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

include_once ('tiki-mytiki_shared.php');

ask_ticket('user-menu');

$smarty->assign('mid', 'tiki-usermenu.tpl');
$smarty->display("tiki.tpl");

?>
