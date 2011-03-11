<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'categories';
require_once ('tiki-setup.php');
include_once ('lib/categories/categlib.php');
include_once ('lib/tree/categ_browse_tree.php');
$access->check_feature('feature_categories');
$access->check_permission('tiki_p_view_category');

$prefsgroups = $prefs['feature_group_watches'];
global $prefsgroups, $tiki_p_admin_users, $tiki_p_admin;

$auto_query_args = array('deep', 'sort_mode', 'offset', 'find', 'type', 'parentId');

// Check for parent category or set to 0 if not present
if (!isset($_REQUEST['parentId'])) {
	$_REQUEST['parentId'] = 0;
}
$smarty->assign('parentId', $_REQUEST['parentId']);
if (isset($_REQUEST['maxRecords']) && ($_REQUEST['maxRecords'] >= 1 || $_REQUEST['maxRecords'] == -1)) {
	$maxRecords = $_REQUEST['maxRecords'];
} else {
	$maxRecords = $prefs['maxRecords'];
}
if (!isset($_REQUEST['sort_mode'])) {
	$sort_mode = 'name_asc';
} else {
	$sort_mode = $_REQUEST['sort_mode'];
}
if (!isset($_REQUEST['offset'])) {
	$offset = 0;
} else {
	$offset = $_REQUEST['offset'];
}
$smarty->assign_by_ref('offset', $offset);
if (!isset($_REQUEST['type'])) {
	$type = '';
} else {
	$type = $_REQUEST['type'];
}
$smarty->assign('type', $type);
if (isset($_REQUEST['find'])) {
	$find = $_REQUEST['find'];
} else {
	$find = '';
}
$smarty->assign('find', $find);
$smarty->assign_by_ref('sort_mode', $sort_mode);
if (isset($_REQUEST['deep']) && $_REQUEST['deep'] == 'on') {
	$deep = 'on';
	$smarty->assign('deep', 'on');
} else {
	$deep = 'off';
	$smarty->assign('deep', 'off');
}
$canView = false;
if (is_array($_REQUEST['parentId'])) {
	Perms::bulk( array( 'type' => 'category' ), 'object', $_REQUEST['parentId'] );
	foreach($_REQUEST['parentId'] as $p) {
		$perms = Perms::get( array( 'type' => 'category', 'object' => $p ) );
		if( $perms->view_category ) {
			$paths[] = $categlib->get_category_path($p);
			$p_info = $categlib->get_category($p);
			$canView = true;
		}
	}
	$smarty->assign('paths', $paths);
	$smarty->assign('headtitle', tra('Categories'));
} else {
	// If the parent category is not zero get the category path
	if ($_REQUEST['parentId']) {
		$perms = Perms::get( array( 'type' => 'category', 'object' => $_REQUEST['parentId'] ) );

		$path = $categlib->get_category_path($_REQUEST['parentId']);
		$p_info = $categlib->get_category($_REQUEST['parentId']);
		$father = $p_info['parentId'];
		$smarty->assign_by_ref('p_info', $p_info);
		$canView = $perms->view_category;
		$smarty->assign('headtitle', tra($p_info['name']));
	} else {
		$path = tra('TOP');
		$father = 0;
		$canView = true;
		$smarty->assign('headtitle', tra('Categories'));
	}
	$smarty->assign('path', $path);
	$smarty->assign('father', $father);
}
if (!$canView) {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra('You do not have permission to view this page.'));
	$smarty->display('error.tpl');
	die;
}
//watches
if ($prefs['feature_user_watches'] == 'y') {
	if ($user && isset($_REQUEST['watch_event'])) {
		if ($_REQUEST['watch_action'] == 'add_desc') {
			$name = tra('Top');
			if ($_REQUEST['watch_object'] != 0) {
				$name = $categlib->get_category_path_string_with_root($_REQUEST['watch_object']);
			}
			$categlib->watch_category_and_descendants($user, $_REQUEST['watch_object'], $name);
		} else if ($_REQUEST['watch_action'] == 'add') {
			$name = tra('Top');
			if ($_REQUEST['watch_object'] != 0) {
				$name = $categlib->get_category_path_string_with_root($user, $_REQUEST['watch_object']);
			}
			$categlib->watch_category($user, $_REQUEST['watch_object'], $name);
		} else if ($_REQUEST['watch_action'] == 'remove_desc') {
			$categlib->unwatch_category_and_descendants($user, $_REQUEST['watch_object']);
		} else if ($_REQUEST['watch_action'] == 'remove') {
			$categlib->unwatch_category($user, $_REQUEST['watch_object']);
		}
	}
}

$ctall = $categlib->get_all_categories_respect_perms(null, 'view_category');

$descendants_curr = $categlib->get_category_descendants($_REQUEST['parentId']);
//user watches on current level
$usercatwatches_curr = $tikilib->get_user_watches($user, 'category_changed');
$eyes_curr = add_watch_icons ($descendants_curr, $usercatwatches_curr, $_REQUEST['parentId'], $_REQUEST['parentId'], $deep, $user);
$smarty->assign_by_ref('eyes_curr', $eyes_curr);

$i = 0;
foreach($ctall as $c) {
	$descendants = $categlib->get_category_descendants($c['categId']);
	$usercatwatches = $tikilib->get_user_watches($user, 'category_changed');
	$eyes = add_watch_icons ($descendants, $usercatwatches, $_REQUEST['parentId'], $c['categId'], $deep, $user);
	$ctall[$i]['eyes'] = $eyes;
	++$i;
}
if ($prefs['feature_phplayers'] == 'y' && $prefs['feature_category_use_phplayers'] == 'y') {
	global $tikiphplayers;
	include_once ('lib/phplayers_tiki/tiki-phplayers.php');
	$urlEnd = "&amp;deep=$deep";
	if ($type) $urlEnd.= "&amp;type=$type";
	if (isset($_REQUEST['expanded'])) $urlEnd.= "||||1";
	$urlEnd.= "\n";
	list($itall, $count) = $tikiphplayers->mkCatEntry(0, ".", '', $ctall, $urlEnd, 'browsedcategory.tpl');
	$smarty->assign('tree', $tikiphplayers->mkmenu($itall, 'treecategories', 'tree'));
} else {
	$tree_nodes = array();
	foreach($ctall as $c) {
		$tree_nodes[] = array(
			'id' => $c['categId'],
			'parent' => $c['parentId'],
			'data' => $c['eyes'].' <a class="catname" href="tiki-browse_categories.php?parentId=' . $c["categId"] . '&amp;deep=' . $deep . '&amp;type=' 
						. urlencode($type) . '">' . htmlspecialchars($c['name']) .'</a> ('.$c['objects'].')', 
		);
	}
	$tm = new CatBrowseTreeMaker('categ');
	$res = $tm->make_tree($_REQUEST['parentId'], $tree_nodes);
	$smarty->assign('tree', $res);
}
$objects = $categlib->list_category_objects($_REQUEST['parentId'], $offset, $maxRecords, $sort_mode, $type, $find, $deep == 'on', (!empty($_REQUEST['and'])) ? true : false);
if ($deep == 'on') {
	for ($i = count($objects['data']) - 1; $i >= 0; --$i) $objects['data'][$i]['categName'] = $tikilib->other_value_in_tab_line($ctall, $objects['data'][$i]['categId'], 'categId', 'name');
}


$smarty->assign_by_ref('objects', $objects['data']);
$smarty->assign_by_ref('cant_pages', $objects['cant']);
$smarty->assign_by_ref('maxRecords', $maxRecords);
include_once ('tiki-section_options.php');
ask_ticket('browse-categories');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
if (isset($_GET['plain'])) {
	header('Content-Type: text/plain');
	foreach($objects['data'] as $object) echo "{$object['categName']}\t{$object['type']}\t{$object['itemId']}\n";
	exit;
} else {
	// Display the template
	$smarty->assign('mid', 'tiki-browse_categories.tpl');
	$smarty->display('tiki.tpl');
}

function add_watch_icons($descendants, $usercatwatches, $requestid, $categid, $deep, $user) {
	global $prefs;
	if (!$user || $prefs["feature_user_watches"] != 'y') {
		 return false;
	}
	global $prefsgroups, $tiki_p_admin_users, $tiki_p_admin, $categlib;
	$section = 'categories';
	require_once ('tiki-setup.php');
	include_once ('lib/categories/categlib.php');
	include_once ('lib/tree/categ_browse_tree.php');
	$nodesc = count($descendants);
	$watch_desc = 'n';
	$watch_this = 'n';
	$eyes = $eyesgroup = '';
	if ($categid == 0) {
		$tip_rem_desc = 'Stop watching all categories';
		$tip_add_desc = 'Watch all categories';
		$tip_group = 'Group watches for all categories';
	} else {
		$tip_rem_desc = 'Stop watching this category and its descendants';
		$tip_add_desc = 'Watch this category and its descendants';
		$tip_group = 'Group watches for this category';
	}
	$eye_rem_desc = '&nbsp;&nbsp;<a href="tiki-browse_categories.php?parentId=' . $requestid . '&amp;watch_event=category_changed&amp;watch_object=' . $categid . '&amp;deep=' . $deep . '&amp;watch_action=remove_desc" class="catname"><img src="pics/icons/no_eye_arrow_down.png" alt="' . tra($tip_rem_desc) . '" style="margin-right:2px" width="14" height="14" title="' . tra($tip_rem_desc) . '" class="catname" /></a>';
	$eye_rem = 	'<a href="tiki-browse_categories.php?parentId=' . $requestid . '&amp;watch_event=category_changed&amp;watch_object=' . $categid . '&amp;deep=' . $deep . '&amp;watch_action=remove" class="catname"><img src="pics/icons/no_eye.png" alt="'.tra("Stop watching this category").'" width="14" style="margin-right:3px" height="14" title="'.tra("Stop watching this category").'" class="catname" /></a>';
	$eye_add_desc = '&nbsp;&nbsp;<a href="tiki-browse_categories.php?parentId=' . $requestid . '&amp;watch_event=category_changed&amp;watch_object=' . $categid . '&amp;deep=' . $deep . '&amp;watch_action=add_desc" class="catname"><img src="pics/icons/eye_arrow_down.png" alt="' . tra($tip_add_desc) . '" style="margin-right:2px" width="14" height="14" title="' . tra($tip_add_desc) . '" class="catname" /></a>';
	$eye_add = 	'<a href="tiki-browse_categories.php?parentId=' . $requestid . '&amp;watch_event=category_changed&amp;watch_object=' . $categid . '&amp;deep=' . $deep . '&amp;watch_action=add" class="icon"><img src="pics/icons/eye.png" alt="'.tra("Watch this category").'" width="14" style="margin-right:3px;margin-bottom:0.052cm" height="14" title="'.tra("Watch this category").'" class="catname" /></a>';
	foreach ($descendants as $descendant) {
		if ($nodesc > 1) {
			//this category and descendants
			foreach ($usercatwatches as $usercatwatch) {
				if ($usercatwatch['object'] == $descendant || $descendant == 0) {
					$watch_desc = 'y';
					break;
				} else {
					$watch_desc = 'n';
				}
			}
			if ($watch_desc == 'n') {
				$eyes = $eye_add_desc; 
				break;
			} else {
				$eyes = $eye_rem_desc;
			}
		}
	}
	//this category only
	foreach ($usercatwatches as $usercatwatch) {
		if ($usercatwatch['object'] == $descendants[0]) {
			$watch_this = 'y';
			break;
		} else {
			$watch_this = 'n';
		}
	}
	if ($categid == 0) {
		$eyes .= '';
	} elseif ($watch_this == 'n') {
		$nodesc > 1 ? $eyes .= $eye_add : $eyes .= '&nbsp;&nbsp;' . $eye_add;
	} else {
		$nodesc > 1 ? $eyes .= $eye_rem : $eyes .= '&nbsp;&nbsp;' . $eye_rem;
	}
	//group watches
	if ($prefsgroups == 'y' && ( $tiki_p_admin_users == 'y' || $tiki_p_admin == 'y' )) {
		$objName = '';
		if ($categid == 0) {
			$objName = 'Top';
		} else {
			$objName = $categlib->get_category_path_string_with_root($categid);
		}
		$eyesgroup = '&nbsp;<a href="tiki-object_watches.php?objectId=' . $categid . '&amp;watch_event=category_changed&amp;objectType=Category&amp;objectName=' 
				. $objName . '&amp;objectHref=tiki-browse_categories.php?parentId=' . $categid . '&amp;deep=' . $deep . '" >
				<img src="pics/icons/eye_group.png" alt="' . tra($tip_group) . '" width="14" style="margin-bottom:2px" height="14" 
				title="' . tra($tip_group) . '" class="catname" /></a>';
	}
	return $eyes . $eyesgroup;
}
