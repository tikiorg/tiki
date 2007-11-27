<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_menu_options.php,v 1.31.2.3 2007-11-27 14:53:11 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/menubuilder/menulib.php');

if ($tiki_p_admin != 'y' && $tiki_p_edit_menu_option != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["menuId"])) {
	$smarty->assign('msg', tra("No menu indicated"));

	$smarty->display("error.tpl");
	die;
}

$maxPos = $menulib->get_max_option($_REQUEST["menuId"]);

$smarty->assign('menuId', $_REQUEST["menuId"]);
$menu_info = $tikilib->get_menu($_REQUEST["menuId"]);
$smarty->assign('menu_info', $menu_info);

if (!isset($_REQUEST["optionId"])) {
	$_REQUEST["optionId"] = 0;
}

$smarty->assign('optionId', $_REQUEST["optionId"]);

if ($_REQUEST["optionId"]) {
	$info = $menulib->get_menu_option($_REQUEST["optionId"]);
} else {
	$info = array();

	$info["name"] = '';
	$info["url"] = '';
	$info["section"] = '';
	$info["perm"] = '';
	$info["groupname"] = '';
	$info["userlevel"] = '';
	$info["type"] = 'o';
	$info["position"] = $maxPos + 2;
}

$smarty->assign('name', $info["name"]);
$smarty->assign('url', $info["url"]);
$smarty->assign('section', $info["section"]);
$smarty->assign('perm', $info["perm"]);
$smarty->assign('type', $info["type"]);
$smarty->assign('position', $info["position"]);
$smarty->assign('groupname', $info["groupname"]);
$smarty->assign('userlevel', $info["userlevel"]);

if (isset($_REQUEST["remove"])) {
  check_ticket('admin-menu-options');
  $area = 'delmenuoption';
  if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
    key_check($area);
		$menulib->remove_menu_option($_REQUEST["remove"]);
		$maxPos = $menulib->get_max_option($_REQUEST["menuId"]);
		$smarty->assign('position', $maxPos + 1);
		$smarty->clear_cache(null, "menu" . $_REQUEST["menuId"]);
  } else {
    key_get($area);
  }
}


if (isset($_REQUEST["up"])) {
	check_ticket('admin-menu-options');
	$res = $menulib->prev_pos($_REQUEST["up"]);
}

if (isset($_REQUEST["down"])) {
  check_ticket('admin-menu-options');
  $area = 'downmenuoption';
	$res = $menulib->next_pos($_REQUEST["down"]);
}

if (isset($_REQUEST['delsel_x']) && isset($_REQUEST['checked'])) {
	check_ticket('admin-menu-options');
	foreach($_REQUEST['checked'] as $id) {
		$menulib->remove_menu_option($id);
	}
	$maxPos = $menulib->get_max_option($_REQUEST['menuId']);
	$smarty->assign('position', $maxPos + 1);
	$smarty->clear_cache(null, 'menu' . $_REQUEST['menuId']);
}

if (isset($_REQUEST["save"])) {
	if (!isset($_REQUEST['groupname']))
		$_REQUEST['groupname'] = '';
	elseif (is_array($_REQUEST['groupname'] ) )
		$_REQUEST['groupname'] = implode(',', $_REQUEST['groupname']);
	if (!isset($_REQUEST['level'])) $_REQUEST['level'] = 0;

include_once('lib/modules/modlib.php');
	check_ticket('admin-menu-options');
	$menulib->replace_menu_option($_REQUEST["menuId"], $_REQUEST["optionId"], $_REQUEST["name"], $_REQUEST["url"],
		$_REQUEST["type"], $_REQUEST["position"], $_REQUEST["section"], $_REQUEST["perm"], $_REQUEST["groupname"], $_REQUEST['level']);
	$modlib->clear_cache();
	$smarty->clear_cache(null, "menu" . $_REQUEST["menuId"]);
	$smarty->assign('position', $_REQUEST["position"] + 1);
	$smarty->assign('name', '');
	$smarty->assign('optionId', 0);
	$smarty->assign('url', '');
	$smarty->assign('section', '');
	$smarty->assign('perm', '');
	$smarty->assign('groupname', '');
	$smarty->assign('userlevel', 0);
	$smarty->assign('type', 'o');
}

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

if (isset($_REQUEST['nbRecords'])) {
	$nbRecords = $_REQUEST['nbRecords'];
	if ($nbRecords != $maxRecords)
		$smarty->assign('nbRecords', $_REQUEST['nbRecords']);
} else {
	$nbRecords = $maxRecords;
}


$smarty->assign_by_ref('sort_mode', $sort_mode);
$allchannels = $menulib->list_menu_options($_REQUEST["menuId"], 0, -1, $sort_mode, $find);
$allchannels = $menulib->sort_menu_options($allchannels);
$channels = $menulib->list_menu_options($_REQUEST["menuId"], $offset, $nbRecords, $sort_mode, $find, true);
$channels = $menulib->describe_menu_types($channels);
$cant_pages = ceil($channels["cant"] / $nbRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $nbRecords));

if ($channels["cant"] > ($offset + $nbRecords)) {
	$smarty->assign('next_offset', $offset + $nbRecords);
} else {
	$smarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($offset > 0) {
	$smarty->assign('prev_offset', $offset - $nbRecords);
} else {
	$smarty->assign('prev_offset', -1);
}

$smarty->assign_by_ref('channels', $channels["data"]);
$smarty->assign_by_ref('allchannels', $allchannels["data"]);

if ( isset($info['groupname']) && ! is_array($info['groupname']) ) $info['groupname'] = explode(',', $info['groupname']);
$all_groups = $userlib->list_all_groups();
if ( is_array($all_groups) )
	foreach ( $all_groups as $g )
		$option_groups[$g] = ( is_array($info['groupname']) && in_array($g, $info['groupname']) ) ? 'selected="selected"' : '';
$smarty->assign_by_ref('option_groups', $option_groups);

ask_ticket('admin-menu-options');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-admin_menu_options.tpl');
$smarty->display("tiki.tpl");

?>
