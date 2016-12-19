<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
$menulib = TikiLib::lib('menu');
$access->check_permission(array('tiki_p_edit_menu_option'));
if (!isset($_REQUEST["menuId"])) {
	$smarty->assign('msg', tra("No menu indicated"));
	$smarty->display("error.tpl");
	die;
}
$auto_query_args = array(
	'offset',
	'find',
	'sort_mode',
	'menuId',
	'maxRecords',
	'preview_css',
	'preview_type',
);

$maxPos = $menulib->get_max_option($_REQUEST["menuId"]);
$smarty->assign('menuId', $_REQUEST["menuId"]);
$editable_menu_info = $menulib->get_menu($_REQUEST["menuId"]);
$smarty->assign('editable_menu_info', $editable_menu_info);
if (!isset($_REQUEST["optionId"])) {
	$_REQUEST["optionId"] = 0;
}
$smarty->assign('optionId', $_REQUEST["optionId"]);
if ($_REQUEST["optionId"]) {
	$info = $menulib->get_menu_option($_REQUEST["optionId"]);
	$cookietab = 2;
} else {
	$info = array();
	$info["name"] = '';
	$info["url"] = '';
	$info["section"] = '';
	$info["perm"] = '';
	$info["groupname"] = '';
	$info["userlevel"] = '';
	$info["type"] = 'o';
	$info["icon"] = '';
	$info["class"] = '';
	$info["position"] = $maxPos + 10;
}
$smarty->assign('name', $info["name"]);
$smarty->assign('url', $info["url"]);
$smarty->assign('section', $info["section"]);
$smarty->assign('perm', $info["perm"]);
$smarty->assign('type', $info["type"]);
$smarty->assign('icon', $info["icon"]);
$smarty->assign('position', $info["position"]);
$smarty->assign('groupname', $info["groupname"]);
$smarty->assign('userlevel', $info["userlevel"]);
$smarty->assign('class', $info["class"]);

if (isset($_REQUEST["remove"])) {
	$access->check_authenticity();
	$menulib->remove_menu_option($_REQUEST["remove"]);
	$maxPos = $menulib->get_max_option($_REQUEST["menuId"]);
	$smarty->assign('position', $maxPos + 10);
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
	foreach ($_REQUEST['checked'] as $id) {
		$menulib->remove_menu_option($id);
	}
	$maxPos = $menulib->get_max_option($_REQUEST['menuId']);
	$smarty->assign('position', $maxPos + 10);
}
if (isset($_REQUEST["save"])) {
	if (!isset($_REQUEST['groupname'])) $_REQUEST['groupname'] = '';
	elseif (is_array($_REQUEST['groupname'])) $_REQUEST['groupname'] = implode(',', $_REQUEST['groupname']);
	if (!isset($_REQUEST['level'])) $_REQUEST['level'] = 0;
	$modlib = TikiLib::lib('mod');
	check_ticket('admin-menu-options');
	$menulib->replace_menu_option($_REQUEST["menuId"], $_REQUEST["optionId"], $_REQUEST["name"], $_REQUEST["url"], $_REQUEST["type"], $_REQUEST["position"], $_REQUEST["section"], $_REQUEST["perm"], $_REQUEST["groupname"], $_REQUEST['level'], $_REQUEST['icon'], $_REQUEST['class']);
	$modlib->clear_cache();
	$smarty->assign('position', $_REQUEST["position"] + 10);
	$smarty->assign('name', '');
	$smarty->assign('optionId', 0);
	$smarty->assign('url', '');
	$smarty->assign('section', '');
	$smarty->assign('perm', '');
	$smarty->assign('groupname', '');
	$smarty->assign('userlevel', 0);
	$smarty->assign('type', 'o');
	$smarty->assign('icon', '');
	$smarty->assign('class', '');
	$cookietab = 1;
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
if (isset($_REQUEST['maxRecords'])) {
	$maxRecords = $_REQUEST['maxRecords'];
} else {
	$maxRecords = $prefs['maxRecords'];
}

$smarty->assign('preview_type', isset($_REQUEST['preview_type']) && $_REQUEST['preview_type'] === 'horiz' ? 'horiz' : 'vert');
$smarty->assign('preview_css', isset($_REQUEST['preview_css']) && $_REQUEST['preview_css'] === 'On' ? 'y' : 'n');

$headerlib->add_js('var permNames = ' . json_encode(TikiLib::lib('user')->get_permission_names_for('all')) . ';');
$feature_prefs = array();
foreach ($prefs as $k => $v) {	// attempt to filter out non-feature prefs (still finds 133!)
	if (strpos($k, 'feature') !== false && preg_match_all('/_/m', $k, $m) === 1) {
		$feature_prefs[] = $k;
	}
}
$headerlib->add_js('var prefNames = ' . json_encode($feature_prefs) . ';');

$smarty->assign_by_ref('maxRecords', $maxRecords);
$smarty->assign_by_ref('sort_mode', $sort_mode);
$allchannels = $menulib->list_menu_options($_REQUEST["menuId"], 0, -1, $sort_mode, $find);
$allchannels = $menulib->sort_menu_options($allchannels);
$channels = $menulib->list_menu_options($_REQUEST["menuId"], $offset, $maxRecords, $sort_mode, $find, true, 0, true);
$channels = $menulib->describe_menu_types($channels);
$smarty->assign_by_ref('cant_pages', $channels["cant"]);
$smarty->assign_by_ref('channels', $channels["data"]);
$smarty->assign_by_ref('allchannels', $allchannels["data"]);
if (isset($info['groupname']) && !is_array($info['groupname'])) $info['groupname'] = explode(',', $info['groupname']);
$all_groups = $userlib->list_all_groups();
if (is_array($all_groups)) foreach ($all_groups as $g) $option_groups[$g] = (is_array($info['groupname']) && in_array($g, $info['groupname'])) ? 'selected="selected"' : '';
$smarty->assign_by_ref('option_groups', $option_groups);

ask_ticket('admin-menu-options');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
// Display the template
$smarty->assign('mid', 'tiki-admin_menu_options.tpl');
$smarty->display("tiki.tpl");
