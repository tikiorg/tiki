<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

include_once ('tiki-setup.php');
$access->check_permission('tiki_p_admin');
$all_perms = $userlib->get_permissions();

function is_perm($permName, $objectType) {
	global $all_perms, $tikilib;
	$permGroup = $tikilib->get_permGroup_from_objectType($objectType);
	foreach($all_perms['data'] as $perm) {
		if ($perm['permName'] == $permName) {
			return $permGroup == $perm['type'];
		}
	}
	return false;
}
function list_perms($objectId, $objectType, $objectName) {
	global $userlib, $tikilib, $prefs;
	$ret = array();
	$cats = array();
	$perms = $userlib->get_object_permissions($objectId, $objectType);
	if (!empty($perms)) {
		foreach($perms as $perm) {
			$ret[] = array('group' => $perm['groupName'], 'perm' => $perm['permName'], 'reason' => 'Object',
					'objectId' => $objectId, 'objectType' => $objectType, 'objectName' => $objectName);
		}
	} elseif ($prefs['feature_categories'] == 'y') {
		global $categlib;
		include_once ('lib/categories/categlib.php');
		$categs = $categlib->get_object_categories($objectType, $objectId);
		if (!empty($categs)) {
			foreach($categs as $categId) {
				$category_perms = $userlib->get_object_permissions($categId, 'category');
				// return array(array('groupName'=>g, 'permName'=>p), ...)
				$config = array();
				if (!empty($category_perms)) {
					foreach($category_perms as $category_perm) {
						if (is_perm($category_perm['permName'], $objectType)) {
							$config[$category_perm['groupName']][$category_perm['permName']] = 'y';
							$cats[] = array('group' => $category_perm['groupName'], 'perm' => $category_perm['permName'],
									'reason' => 'Category', 'objectId' => $categId, 'objectType' => 'category',
									'objectName' => $categlib->get_category_name($categId));
						}
					}
				}
			}
		}
	}
	return array('objectId' => $objectId, 'special' => $ret, 'category' => $cats);
}
$types = array('wiki page', 'file gallery', 'tracker', 'forum', 'group');
include_once ("lib/comments/commentslib.php"); global $commentslib; $commentslib = new Comments($dbTiki);
$all_groups = $userlib->list_all_groups();
$res = array();
foreach($types as $type) {
	$type_perms = $userlib->get_permissions(0, -1, 'permName_asc', '', $tikilib->get_permGroup_from_objectType($type));
	foreach($all_groups as $gr) {
		$perms = $userlib->get_group_permissions($gr);
		foreach($type_perms['data'] as $type_perm) {
			if (in_array($type_perm['permName'], $perms)) {
				$res[$type]['default'][] = array('group' => $gr, 'perm' => $type_perm['permName']);
			}
		}
	}
	switch ($type) {
		case 'wiki page':
		case 'wiki':
			$objects = $tikilib->list_pageNames();
			foreach($objects['data'] as $object) {
				$r = list_perms($object['pageName'], $type, $object['pageName']);
				if (count($r['special']) > 0) { $res[$type]['objects'][] = array('objectId' => $r['objectId'], 'special' => $r['special']); }
				if (count($r['category']) > 0) { $res[$type]['category'][] = array('objectId' => $r['objectId'], 'category' => $r['category']); }
			}
			break;

		case 'file galleries':
		case 'file gallery':
			$filegallib = TikiLib::lib('filegal');
			$objects = $filegallib->list_file_galleries( 0, -1, 'name_asc', '', '', $prefs['fgal_root_id'] );
			foreach($objects['data'] as $object) {
				$r = list_perms($object['id'], $type, $object['name']);
				if (count($r['special']) > 0) { $res[$type]['objects'][] = array('objectId' => $r['objectId'], 'special' => $r['special'], 'objectName' => $object['name']); }
				if (count($r['category']) > 0) { $res[$type]['category'][] = array('objectId' => $r['objectId'], 'category' => $r['category'], 'objectName' => $object['name']); }
			}
			break;

		case 'tracker':
		case 'trackers':
			$objects = TikiLib::lib('trk')->list_trackers();
			foreach($objects['data'] as $object) {
				$r = list_perms($object['trackerId'], $type, $object['name']);
				if (count($r['special']) > 0) { $res[$type]['objects'][] = array('objectId' => $r['objectId'], 'special' => $r['special'], 'objectName' => $object['name']); }
				if (count($r['category']) > 0) { $res[$type]['category'][] = array('objectId' => $r['objectId'], 'category' => $r['category'], 'objectName' => $object['name']); }
			}
			break;

		case 'forum':
		case 'forums':
			$objects = $commentslib->list_forums();
			foreach($objects['data'] as $object) {
				$r = list_perms($object['forumId'], $type, $object['name']);
				if (count($r['special']) > 0) { $res[$type]['objects'][] = array('objectId' => $r['objectId'], 'special' => $r['special'], 'objectName' => $object['name']); }
				if (count($r['category']) > 0) { $res[$type]['category'][] = array('objectId' => $r['objectId'], 'category' => $r['category'], 'objectName' => $object['name']); }
			}
			break;

		case 'group':
		case 'groups':
			foreach($all_groups as $object) {
				$r = list_perms($object, $type, '');
				if (count($r['special']) > 0) { $res[$type]['objects'][] = array('objectId' => $r['objectId'], 'special' => $r['special']); }
				if (count($r['category']) > 0) { $res[$type]['category'][] = array('objectId' => $r['objectId'], 'category' => $r['category']); }
			}
			break;

			default:
			break;
	}
}
$smarty->assign_by_ref('res', $res);
$smarty->assign('mid', 'tiki-list_object_permissions.tpl');
$smarty->display('tiki.tpl');
