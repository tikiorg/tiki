<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_modules.php,v 1.32 2004-06-10 16:15:39 sylvieg Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

include_once ('lib/menubuilder/menulib.php');
include_once ('lib/rss/rsslib.php');
include_once ('lib/polls/polllib.php');
include_once ('lib/banners/bannerlib.php');
include_once ('lib/dcs/dcslib.php');
include_once ('lib/modules/modlib.php');

if (!isset($dcslib)) {
	$dcslib = new DCSLib($dbTiki);
}

if (!isset($bannerlib)) {
	$bannerlib = new BannerLib($dbTiki);
}

if (!isset($rsslib)) {
	$rsslib = new RssLib($dbTiki);
}

if (!isset($polllib)) {
	$polllib = new PollLib($dbTiki);
}

$smarty->assign('wysiwyg', 'n');

if (isset($_REQUEST['wysiwyg']) && $_REQUEST['wysiwyg'] == 'y') {
	$smarty->assign('wysiwyg', 'y');
}

// PERMISSIONS: NEEDS p_admin
if ($user != 'admin') {
	if ($tiki_p_admin != 'y') {
		$smarty->assign('msg', tra("You dont have permission to use this feature"));

		$smarty->display("error.tpl");
		die;
	}
}

// Values for the user_module edit/create form
$smarty->assign('um_name', '');
$smarty->assign('um_title', '');
$smarty->assign('um_data', '');

$smarty->assign('assign_name', '');
//$smarty->assign('assign_title','');
$smarty->assign('assign_position', '');
$smarty->assign('assign_order', '');
$smarty->assign('assign_cache', 0);
$smarty->assign('assign_rows', 10);
$smarty->assign('assign_params', '');

if (isset($_REQUEST["clear_cache"])) {
	check_ticket('admin-modules');
	$modlib->clear_cache(); 
}

$module_groups = array();

if (isset($_REQUEST["edit_assign"])) {
	check_ticket('admin-modules');
	$_REQUEST["edit_assign"] = urldecode($_REQUEST["edit_assign"]);

	$info = $modlib->get_assigned_module($_REQUEST["edit_assign"]);
	$grps = '';

	if ($info["groups"]) {
		$module_groups = unserialize($info["groups"]);

		foreach ($module_groups as $amodule) {
			$grps = $grps . ' $amodule ';
		}
	}

	if (!isset($info['rows']) || empty($info['rows'])) {
		$info['rows'] = 0;
	}

	$smarty->assign('module_groups', $grps);
	$smarty->assign_by_ref('assign_name', $info["name"]);
	//$smarty->assign_by_ref('assign_title',$info["title"]);
	$smarty->assign_by_ref('assign_position', $info["position"]);
	$smarty->assign_by_ref('assign_cache', $info["cache_time"]);
	$smarty->assign_by_ref('assign_rows', $info["rows"]);
	$smarty->assign_by_ref('assign_params', $info["params"]);
	$smarty->assign_by_ref('assign_type', $info["type"]);

	if (isset($info["ord"])) {
		$cosa = "" . $info["ord"];
	} else {
		$cosa = "";
	}

	$smarty->assign_by_ref('assign_order', $cosa);
}

if (isset($_REQUEST["unassign"])) {
	check_ticket('admin-modules');
	$_REQUEST["unassign"] = urldecode($_REQUEST["unassign"]);

	$modlib->unassign_module($_REQUEST["unassign"]);
	$logslib->add_log('adminmodules','unassigned module '.$_REQUEST["unassign"]);
}

if (isset($_REQUEST["modup"])) {
	check_ticket('admin-modules');
	$_REQUEST["modup"] = urldecode($_REQUEST["modup"]);

	$modlib->module_up($_REQUEST["modup"]);
}

if (isset($_REQUEST["moddown"])) {
	check_ticket('admin-modules');
	$_REQUEST["moddown"] = urldecode($_REQUEST["moddown"]);

	$modlib->module_down($_REQUEST["moddown"]);
}

/* Edit or delete a user module */
if (isset($_REQUEST["um_update"])) {
	check_ticket('admin-modules');
	$_REQUEST["um_update"] = urldecode($_REQUEST["um_update"]);

	$smarty->assign_by_ref('um_name', $_REQUEST["um_name"]);
	$smarty->assign_by_ref('um_title', $_REQUEST["um_title"]);
	$smarty->assign_by_ref('um_data', $_REQUEST["um_data"]);
	$modlib->replace_user_module(preg_replace("/\W/", "_",$_REQUEST["um_name"]), $_REQUEST["um_title"], $_REQUEST["um_data"]);
	$logslib->add_log('adminmodules','changed user module '.$_REQUEST["um_name"]);
}

if (!isset($_REQUEST["groups"])) {
	$_REQUEST["groups"] = array();
}

$smarty->assign('preview', 'n');

if (isset($_REQUEST["preview"])) {
	check_ticket('admin-modules');
	$smarty->assign('preview', 'y');

	$smarty->assign_by_ref('assign_name', $_REQUEST["assign_name"]);

	if ($tikilib->is_user_module($_REQUEST["assign_name"])) {
		$info = $tikilib->get_user_module($_REQUEST["assign_name"]);

		$smarty->assign_by_ref('user_title', $info["title"]);
		$smarty->assign_by_ref('user_data', $info["data"]);
		$data = $smarty->fetch('modules/user_module.tpl');
	} else {
		$phpfile = 'modules/mod-' . $_REQUEST["assign_name"] . '.php';

		$template = 'modules/mod-' . $_REQUEST["assign_name"] . '.tpl';

		if (file_exists($phpfile)) {
			$module_rows = $_REQUEST["assign_rows"];

			parse_str($_REQUEST["assign_params"], $module_params);
			include ($phpfile);
		}

		if (file_exists('templates/' . $template)) {
			$data = $smarty->fetch($template);
		} else {
			$data = '';
		}
	}

	$smarty->assign_by_ref('assign_name', $_REQUEST["assign_name"]);
	$smarty->assign_by_ref('assign_params', $_REQUEST["assign_params"]);
	$smarty->assign_by_ref('assign_position', $_REQUEST["assign_position"]);
	$smarty->assign_by_ref('assign_order', $_REQUEST["assign_order"]);
	$smarty->assign_by_ref('assign_cache', $_REQUEST["assign_cache"]);
	$smarty->assign_by_ref('assign_rows', $_REQUEST["assign_rows"]);
	$module_groups = $_REQUEST["groups"];
	$grps = '';

	foreach ($module_groups as $amodule) {
		$grps = $grps . " $amodule ";
	}

	$smarty->assign('module_groups', $grps);
	$smarty->assign_by_ref('preview_data', $data);
}

if (isset($_REQUEST["assign"])) {
	check_ticket('admin-modules');
	$_REQUEST["assign"] = urldecode($_REQUEST["assign"]);

	$smarty->assign_by_ref('assign_name', $_REQUEST["assign_name"]);
	//$smarty->assign_by_ref('assign_title',$_REQUEST["assign_title"]);
	$smarty->assign_by_ref('assign_position', $_REQUEST["assign_position"]);
	$smarty->assign_by_ref('assign_params', $_REQUEST["assign_params"]);
	$smarty->assign_by_ref('assign_order', $_REQUEST["assign_order"]);
	$smarty->assign_by_ref('assign_cache', $_REQUEST["assign_cache"]);
	$smarty->assign_by_ref('assign_rows', $_REQUEST["assign_rows"]);
 	$smarty->assign_by_ref('assign_type',$_REQUEST["assign_type"]);
	$module_groups = $_REQUEST["groups"];
	$grps = '';

	foreach ($module_groups as $amodule) {
		$grps = $grps . " $amodule ";
	}

	$smarty->assign('module_groups', $grps);
	$modlib->assign_module($_REQUEST["assign_name"],
		'', $_REQUEST["assign_position"], $_REQUEST["assign_order"], $_REQUEST["assign_cache"], $_REQUEST["assign_rows"],
		serialize($module_groups), $_REQUEST["assign_params"], $_REQUEST["assign_type"]);
	$logslib->add_log('adminmodules','assigned module '.$_REQUEST["assign_name"]);
	header ("location: tiki-admin_modules.php");
}

if (isset($_REQUEST["um_remove"])) {
	check_ticket('admin-modules');
	$_REQUEST["um_remove"] = urldecode($_REQUEST["um_remove"]);

	$modlib->remove_user_module($_REQUEST["um_remove"]);
	$logslib->add_log('adminmodules','removed user module '.$_REQUEST["um_remove"]);
}

if (isset($_REQUEST["um_edit"])) {
	check_ticket('admin-modules');
	$_REQUEST["um_edit"] = urldecode($_REQUEST["um_edit"]);

	$um_info = $tikilib->get_user_module($_REQUEST["um_edit"]);
	$smarty->assign_by_ref('um_name', $um_info["name"]);
	$smarty->assign_by_ref('um_title', $um_info["title"]);
	$smarty->assign_by_ref('um_data', $um_info["data"]);
}

$user_modules = $modlib->list_user_modules();
$smarty->assign_by_ref('user_modules', $user_modules["data"]);

$all_modules = $modlib->get_all_modules();
sort ($all_modules);
$smarty->assign_by_ref('all_modules', $all_modules);

$orders = array();

for ($i = 1; $i < 50; $i++) {
	$orders[] = $i;
}

$smarty->assign_by_ref('orders', $orders);

$groups = $userlib->list_all_groups();
$allgroups = array();
for ($i = 0; $i < count($groups); $i++) {
	if (in_array($groups[$i], $module_groups)) {
		$allgroups[$i]["groupName"] = $groups[$i];
		$allgroups[$i]["selected"] = 'y';
	} else {
		$allgroups[$i]["groupName"] = $groups[$i];
		$allgroups[$i]["selected"] = 'n';
	}
}

$smarty->assign("groups", $allgroups);
$galleries = $tikilib->list_galleries(0, -1, 'lastModif_desc', $user, '');
$smarty->assign('galleries', $galleries["data"]);
$polls = $polllib->list_active_polls(0, -1, 'publishDate_desc', '');
$smarty->assign('polls', $polls["data"]);
$contents = $dcslib->list_content(0, -1, 'contentId_desc', '');
$smarty->assign('contents', $contents["data"]);
$rsss = $rsslib->list_rss_modules(0, -1, 'name_desc', '');
$smarty->assign('rsss', $rsss["data"]);
$menus = $menulib->list_menus(0, -1, 'menuId_desc', '');
$smarty->assign('menus', $menus["data"]);
$banners = $bannerlib->list_zones();
$smarty->assign('banners', $banners["data"]);
$left = $tikilib->get_assigned_modules('l');
$right = $tikilib->get_assigned_modules('r');
$smarty->assign_by_ref('left', $left);
$smarty->assign_by_ref('right', $right);

$sameurl_elements = array(
	'offset',
	'sort_mode',
	'where',
	'find'
);
ask_ticket('admin-modules');
$smarty->assign('mid', 'tiki-admin_modules.tpl');
$smarty->display("tiki.tpl");

?>
