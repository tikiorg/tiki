<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

include_once ('tiki-setup.php');
global $categlib;
include_once ('lib/categories/categlib.php');
$access->check_feature('feature_group_watches');
$access->check_permission(array('tiki_p_admin_users'));
if (!isset($_REQUEST['objectId']) || empty($_REQUEST['objectType']) || !isset($_REQUEST['objectName']) 
	|| !isset($_REQUEST['watch_event']) || !isset($_REQUEST['objectHref'])
	) {
	$smarty->assign('msg', tra('Not enough information to display this page'));
	$smarty->display('error.tpl');
	die;
}
$auto_query_args = array('objectId', 'objectType', 'objectName', 'watch_event', 'referer', 'objectHref');
$all_groups = $userlib->list_all_groups();
$smarty->assign_by_ref('all_groups', $all_groups);
$smarty->assign_by_ref('objectType', strtolower($_REQUEST['objectType']));
if ($_REQUEST['objectType'] == 'Category') {
	$smarty->assign('cat', 'y');
	$desc_cnt = $categlib->get_category_descendants($_REQUEST['objectId']);
	if (count($desc_cnt) > 1) {
		$smarty->assign('desc', 'y');
	}
	if ($_REQUEST['objectId'] == 0) {
		$smarty->assign('isTop', 'y');
	}
}

if (!isset($_REQUEST['referer']) && isset($_SERVER['HTTP_REFERER'])) {
	$_REQUEST['referer'] = $_SERVER['HTTP_REFERER'];
}
if (isset($_REQUEST['referer'])) {
	$smarty->assign('referer', $_REQUEST['referer']);
}

if (isset($_REQUEST['assign'])) {
	$addedGroups = array();
	$deletedGroups = array();
	if (!isset($_REQUEST['checked'])) $_REQUEST['checked'] = array();
	$old_watches = $tikilib->get_groups_watching($_REQUEST['objectType'], $_REQUEST['objectId'], $_REQUEST['watch_event']);
	check_ticket('object_watches');
	foreach($all_groups as $g) {
		if (in_array($g, $_REQUEST['checked']) && !in_array($g, $old_watches)) {
			$tikilib->add_group_watch($g, $_REQUEST['watch_event'], $_REQUEST['objectId'], $_REQUEST['objectType'], 
				$_REQUEST['objectName'], $_REQUEST['objectHref']);
			$addedGroups[] = $g;
		} elseif (!in_array($g, $_REQUEST['checked']) && in_array($g, $old_watches)) {
			$tikilib->remove_group_watch($g, $_REQUEST['watch_event'], $_REQUEST['objectId'], $_REQUEST['objectType'], 
				$_REQUEST['objectName'], $_REQUEST['objectHref']);
			$deletedGroups[] = $g;
		}
		$smarty->assign_by_ref('addedGroups', $addedGroups);
		$smarty->assign_by_ref('deletedGroups', $deletedGroups);
		$group_watches = $_REQUEST['checked'];
	}
	if 	($_REQUEST['objectType'] == 'Category') {
		global $descendants;	
		$addedGroupsDesc = array();
		$deletedGroupsDesc = array();
		$catTreeNodes = array();
		foreach($all_groups as $g) {
			if (isset($_REQUEST[$g]) && $_REQUEST[$g] == 'cat_add_desc') {
				$categlib->group_watch_category_and_descendants($g, $_REQUEST['objectId'], $_REQUEST['objectName'], false);
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
		
		if (count($descendants) > 0) {
			foreach($descendants as $d) {
				if ($d != 0) {
					$catinfo = $categlib->get_category($d);
					$catTreeNodes[] = array(
						'id' => $catinfo['categId'],
						'parent' => $catinfo['parentId'],
						'data' => $catinfo['name'], 
					);
				}
				include_once('lib/tree/categ_browse_tree.php');
				$tm = new CatBrowseTreeMaker('categ');
				$res = $tm->make_tree($catTreeNodes[0]['parent'], $catTreeNodes);
				$smarty->assign('tree', $res);
				$smarty->assign_by_ref('catTreeNodes', $catTreeNodes);
			}
		}
	}
} else {
	$group_watches = $tikilib->get_groups_watching($_REQUEST['objectType'], $_REQUEST['objectId'], $_REQUEST['watch_event']);
}

$smarty->assign_by_ref('group_watches', $group_watches);
ask_ticket('object_watches');
$smarty->assign('mid', 'tiki-object_watches.tpl');
$smarty->display('tiki.tpl');
