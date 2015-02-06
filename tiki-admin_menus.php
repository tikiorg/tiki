<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
$menulib = TikiLib::lib('menu');
$auto_query_args = array(
	'offset',
	'sort_mode',
	'menuId'
);
$access->check_permission(array('tiki_p_edit_menu'));

if (!isset($_REQUEST['menuId'])) {
	$_REQUEST['menuId'] = 0;
}
$smarty->assign('menuId', $_REQUEST['menuId']);

if ($_REQUEST['menuId']) {
	$info = $menulib->get_menu($_REQUEST['menuId']);
} else {
	$info = array();
	$info['name'] = '';
	$info['description'] = '';
	$info['type'] = 'd';
	$info['icon'] = null;
	$info['use_items_icons'] = 'n';
	$info['parse'] = 'n';
}
$smarty->assign_by_ref('info', $info);

if (isset($_REQUEST['remove'])) {
	$access->check_authenticity(tra('Are you sure you want to delete menu id:') . ' ' . $_REQUEST['remove']);
	$menulib->remove_menu($_REQUEST['remove']);
}

if (isset($_REQUEST['save'])) {
	$access->check_ticket();
	if (!isset($_REQUEST['icon'])) $_REQUEST['icon'] = null;
	$_REQUEST['use_items_icons'] = (isset($_REQUEST['use_items_icons']) && $_REQUEST['use_items_icons'] == 'on') ? 'y' : 'n';
	$_REQUEST['parse'] = (isset($_REQUEST['parse']) && $_REQUEST['parse'] == 'on') ? 'y' : 'n';
	$menulib->replace_menu($_REQUEST['menuId'], $_REQUEST['name'], $_REQUEST['description'], $_REQUEST['type'], $_REQUEST['icon'], $_REQUEST['use_items_icons'], $_REQUEST['parse']);
	$_REQUEST['menuId'] = 0;
	$smarty->assign('menuId', 0);
	$smarty->assign(
		'info',
		array(
			'name' => '',
			'description' => '',
			'type' => 'd',
			'icon' => null,
			'use_items_icons' => 'n',
			'parse' => 'n',
		)
	);
}

if (isset($_REQUEST['clone']) && $_REQUEST['menuId'] > 0) {
	check_ticket('admin-menus');
	$menulib->clone_menu($_REQUEST['menuId']);
}

if (isset($_REQUEST['reset'])) {
	$access->check_authenticity(tra('Are you sure you want to reset the Application Menu to the current system default?'));
	$menulib->reset_app_menu();
}

if (!isset($_REQUEST['sort_mode'])) {
	$sort_mode = 'name_desc';
} else {
	$sort_mode = $_REQUEST['sort_mode'];
}
$smarty->assign_by_ref('sort_mode', $sort_mode);

if (!isset($_REQUEST['offset'])) {
	$offset = 0;
} else {
	$offset = $_REQUEST['offset'];
}
$smarty->assign_by_ref('offset', $offset);

if (isset($_REQUEST['find'])) {
	$find = $_REQUEST['find'];
} else {
	$find = '';
}
$smarty->assign('find', $find);

$channels = $menulib->list_menus($offset, $maxRecords, $sort_mode, $find);
foreach ($channels['data'] as $i => $channel) {
	if ($userlib->object_has_one_permission($channel['menuId'], 'menus')) {
		$channels['data'][$i]['individual'] = 'y';
	}
}
$smarty->assign_by_ref('cant', $channels['cant']);
$smarty->assign_by_ref('channels', $channels['data']);

ask_ticket('admin-menus');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
// Display the template
$smarty->assign('mid', 'tiki-admin_menus.tpl');
$smarty->display('tiki.tpl');
