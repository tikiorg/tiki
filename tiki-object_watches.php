<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

include_once ('tiki-setup.php');
$categlib = TikiLib::lib('categ');
$access->check_feature('feature_group_watches');
$access->check_permission(array('tiki_p_admin_users'));
if (!isset($_REQUEST['objectId']) || !isset($_REQUEST['watch_event'])) {
	$smarty->assign('msg', tra('Not enough information to display this page'));
	$smarty->display('error.tpl');
	die;
}

$objectType = isset($_REQUEST['objectType']) ? $_REQUEST['objectType'] : null; 

$auto_query_args = array('objectId', 'objectType', 'objectName', 'watch_event', 'referer', 'objectHref');
$all_groups = $userlib->list_all_groups();
$smarty->assign_by_ref('all_groups', $all_groups);
if ($objectType == 'Category') {
	$smarty->assign('cat', 'y');
	$categoryIdentifier = $_REQUEST['objectId'];
	if ($categoryIdentifier) {
		$category = $categlib->get_category($_REQUEST['objectId']);
		$extendedTargets = $category['descendants'];
		$smarty->assign('isTop', 'n');
	} else {
		$extendedTargets = $categlib->getCategories();
		$smarty->assign('isTop', 'y');
	}
	if (count($extendedTargets) > 0) {
		$smarty->assign('desc', 'y');
	}
}

if (!isset($_REQUEST['referer']) && isset($_SERVER['HTTP_REFERER'])) {
	$_REQUEST['referer'] = $_SERVER['HTTP_REFERER'];
}
if (isset($_REQUEST['referer'])) {
	$smarty->assign('referer', $_REQUEST['referer']);
}

if (isset($_REQUEST['assign'])) {
	$objectName = isset($_REQUEST['objectName']) ? $_REQUEST['objectName'] : NULL;
	$objectHref = isset($_REQUEST['objectHref']) ? $_REQUEST['objectHref'] : NULL;
	$addedGroups = array();
	$deletedGroups = array();
	if (!isset($_REQUEST['checked'])) $_REQUEST['checked'] = array();
	$old_watches = $tikilib->get_groups_watching($_REQUEST['objectId'], $_REQUEST['watch_event'], $objectType);
	check_ticket('object_watches');
	foreach ($all_groups as $g) {
		if (in_array($g, $_REQUEST['checked']) && !in_array($g, $old_watches)) {
			$tikilib->add_group_watch($g, $_REQUEST['watch_event'], $_REQUEST['objectId'], $objectType, $objectName, $objectHref);
			$addedGroups[] = $g;
		} elseif (!in_array($g, $_REQUEST['checked']) && in_array($g, $old_watches)) {
			$tikilib->remove_group_watch($g, $_REQUEST['watch_event'], $_REQUEST['objectId'], $objectType);
			$deletedGroups[] = $g;
		}
		$smarty->assign_by_ref('addedGroups', $addedGroups);
		$smarty->assign_by_ref('deletedGroups', $deletedGroups);
		$group_watches = $_REQUEST['checked'];
	}
	if ($objectType == 'Category') {
		$addedGroupsDesc = array();
		$deletedGroupsDesc = array();
		$catTreeNodes = array();
		foreach ($all_groups as $g) {
			if (isset($_REQUEST[$g]) && $_REQUEST[$g] == 'cat_add_desc') {
				$categlib->group_watch_category_and_descendants($g, $_REQUEST['objectId'], $objectName, false);
				if ($g != 'Anonymous') {
					$addedGroupsDesc[] = $g;
				}
			}
			if (isset($_REQUEST[$g]) && $_REQUEST[$g] == 'cat_remove_desc') {
				$categlib->group_unwatch_category_and_descendants($g, $_REQUEST['objectId'], false);
				if ($g != 'Anonymous') {
					$deletedGroupsDesc[] = $g;
				}
			}
		}
		$smarty->assign_by_ref('addedGroupsDesc', $addedGroupsDesc);
		$smarty->assign_by_ref('deletedGroupsDesc', $deletedGroupsDesc);
		
		if (!empty($addedGroupsDesc) || !empty($deletedGroupsDesc)) {
			foreach ($extendedTargets as $d) {
				$catinfo = $categlib->get_category($d);
				$catTreeNodes[] = array(
					'id' => $catinfo['categId'],
					'parent' => $catinfo['parentId'],
					'data' => $catinfo['name'], 
				);
			}
			include_once('lib/tree/BrowseTreeMaker.php');
			$tm = new BrowseTreeMaker('categ');
			$res = $tm->make_tree($catTreeNodes[0]['parent'], $catTreeNodes);
			$smarty->assign('tree', $res);
		}
	}
} else {
	$group_watches = $tikilib->get_groups_watching($_REQUEST['objectId'], $_REQUEST['watch_event'], $objectType);
}

$smarty->assign_by_ref('group_watches', $group_watches);
ask_ticket('object_watches');
$smarty->assign('mid', 'tiki-object_watches.tpl');
$smarty->display('tiki.tpl');
