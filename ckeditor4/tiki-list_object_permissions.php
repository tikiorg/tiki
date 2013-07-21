<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

include_once ('tiki-setup.php');
$access->check_permission('tiki_p_admin');
$all_perms = $userlib->get_permissions();

/**
 * @param $permName
 * @param $objectType
 * @return bool
 */
function is_perm($permName, $objectType)
{
	global $all_perms, $tikilib;
	$permGroup = $tikilib->get_permGroup_from_objectType($objectType);
	foreach ($all_perms['data'] as $perm) {
		if ($perm['permName'] == $permName) {
			return $permGroup == $perm['type'];
		}
	}
	return false;
}

/**
 * @param $objectId
 * @param $objectType
 * @param $objectName
 * @param string $filterGroup
 * @return array
 */
function list_perms($objectId, $objectType, $objectName, $filterGroup='')
{
	global $userlib, $prefs;
	$ret = array();
	$cats = array();
	$perms = $userlib->get_object_permissions($objectId, $objectType);
	if (!empty($perms)) {
		foreach ($perms as $perm) {
			if (empty($filterGroup) || in_array($perm['groupName'], $filterGroup)) {
				$json = json_encode(array('group' => $perm['groupName'], 'perm' => $perm['permName'], 'objectId' => $objectId, 'objectType' => $objectType));
				$ret[] = array('group' => $perm['groupName'], 'perm' => $perm['permName'], 'reason' => 'Object',
						   'objectId' => $objectId, 'objectType' => $objectType, 'objectName' => $objectName, 'json' => $json);
			}
		}
	} elseif ($prefs['feature_categories'] == 'y') {
		global $categlib;
		include_once ('lib/categories/categlib.php');
		$categs = $categlib->get_object_categories($objectType, $objectId);
		if (!empty($categs)) {
			foreach ($categs as $categId) {
				$category_perms = $userlib->get_object_permissions($categId, 'category');
				if (!empty($category_perms)) {
					foreach ($category_perms as $category_perm) {
						if (is_perm($category_perm['permName'], $objectType) && (empty($filterGroup) || in_array($category_perm['groupName'], $filterGroup))) {
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

$filterGroup = empty($_REQUEST['filterGroup']) ? array() : $_REQUEST['filterGroup'];
$feedbacks = array();
$del = !empty($_REQUEST['delsel_x']) || !empty($_REQUEST['delsel']);
$dup = !empty($_REQUEST['dupsel']);
if ($del || $dup) {
	$access->check_authenticity();
	if (!empty($_REQUEST['groupPerm'])) {
		foreach ($_REQUEST['groupPerm'] as $perm) {
			$perm = json_decode($perm, true);
			if ($del) {
				$userlib->remove_permission_from_group($perm['perm'], $perm['group']);
				$feedbacks[] = tra('Remove permission %0 from %1', '', false, array($perm['perm'], $perm['group']));
			} elseif (!empty($_REQUEST['toGroup']) && $userlib->group_exists($_REQUEST['toGroup'])) {
				$userlib->assign_permission_to_group($perm['perm'], $_REQUEST['toGroup']);
				$feedbacks[] = tra('Assign permission %0 to %1', '', false, array($perm['perm'], $_REQUEST['toGroup']));
			}
		}
	}
	if (!empty($_REQUEST['objectPerm'])) {
		foreach ($_REQUEST['objectPerm'] as $perm) {
			$perm = json_decode($perm, true);
			if ($del) {
				$userlib->remove_object_permission($perm['group'], $perm['objectId'], $perm['objectType'], $perm['perm']);
				$feedbacks[] = tra('Remove permission %0 from %1', '', false, array($perm['perm'], $perm['group']));
			} elseif (!empty($_REQUEST['toGroup']) && $userlib->group_exists($_REQUEST['toGroup'])) {
				$userlib->assign_object_permission($_REQUEST['toGroup'], $perm['objectId'], $perm['objectType'], $perm['perm']);
				$feedbacks[] = tra('Assign permission %0 to %1', '', false, array($perm['perm'], $_REQUEST['toGroup']));
			}
		}
	}
	if (!empty($feedbacks) && $dup && !empty($_REQUEST['toGroup']) && !empty($filterGroup) && !in_array($_REQUEST['toGroup'], $filterGroup)) {
		$filterGroup[] = $_REQUEST['toGroup'];
	}
}

$types = array('wiki page', 'file gallery', 'tracker', 'forum', 'group');
$commentslib = TikiLib::lib('comments');
$all_groups = $userlib->list_all_groups();
$res = array();
foreach ($types as $type) {
	$res[$type]['default'] = array();
	$type_perms = $userlib->get_permissions(0, -1, 'permName_asc', '', $tikilib->get_permGroup_from_objectType($type));
	foreach ($all_groups as $gr) {
		$perms = $userlib->get_group_permissions($gr);
		foreach ($type_perms['data'] as $type_perm) {
			if (in_array($type_perm['permName'], $perms) && (empty($filterGroup) || in_array($gr, $filterGroup))) {
				$res[$type]['default'][] = array('group' => $gr, 'perm' => $type_perm['permName']);
			}
		}
	}
	$res[$type]['objects'] = array();
	$res[$type]['category'] = array();
	switch ($type) {
		case 'wiki page':
		case 'wiki':
			$objects = $tikilib->list_pageNames();
			foreach ($objects['data'] as $object) {
				$r = list_perms($object['pageName'], $type, $object['pageName'], $filterGroup);
				if (count($r['special']) > 0) {
					$res[$type]['objects'][] = array('objectId' => $r['objectId'], 'special' => $r['special'], 'objectType' => $type);
				}
				if (count($r['category']) > 0) {
					$res[$type]['category'][] = array('objectId' => $r['objectId'], 'category' => $r['category']);
				}
			}
    		break;

		case 'file galleries':
		case 'file gallery':
			$filegallib = TikiLib::lib('filegal');
			$objects = $filegallib->list_file_galleries(0, -1, 'name_asc', '', '', $prefs['fgal_root_id']);
			foreach ($objects['data'] as $object) {
				$r = list_perms($object['id'], $type, $object['name'], $filterGroup);
				if (count($r['special']) > 0) {
					$res[$type]['objects'][] = array('objectId' => $r['objectId'], 'special' => $r['special'], 'objectName' => $object['name'], 'objectType' => $type);
				}
				if (count($r['category']) > 0) {
					$res[$type]['category'][] = array('objectId' => $r['objectId'], 'category' => $r['category'], 'objectName' => $object['name']);
				}
			}
    		break;

		case 'tracker':
		case 'trackers':
			$objects = TikiLib::lib('trk')->list_trackers();
			foreach ($objects['data'] as $object) {
				$r = list_perms($object['trackerId'], $type, $object['name'], $filterGroup);
				if (count($r['special']) > 0) {
					$res[$type]['objects'][] = array('objectId' => $r['objectId'], 'special' => $r['special'], 'objectName' => $object['name'], 'objectType' => $type);
				}
				if (count($r['category']) > 0) {
					$res[$type]['category'][] = array('objectId' => $r['objectId'], 'category' => $r['category'], 'objectName' => $object['name']);
				}
			}
    		break;

		case 'forum':
		case 'forums':
			$objects = $commentslib->list_forums();
			foreach ($objects['data'] as $object) {
				$r = list_perms($object['forumId'], $type, $object['name'], $filterGroup);
				if (count($r['special']) > 0) {
					$res[$type]['objects'][] = array('objectId' => $r['objectId'], 'special' => $r['special'], 'objectName' => $object['name']);
				}
				if (count($r['category']) > 0) {
					$res[$type]['category'][] = array('objectId' => $r['objectId'], 'category' => $r['category'], 'objectName' => $object['name']);
				}
			}
    		break;

		case 'group':
		case 'groups':
			foreach ($all_groups as $object) {
				$r = list_perms($object, $type, '', $filterGroup);
				if (count($r['special']) > 0) {
					$res[$type]['objects'][] = array('objectId' => $r['objectId'], 'special' => $r['special']);
				}
				if (count($r['category']) > 0) {
					$res[$type]['category'][] = array('objectId' => $r['objectId'], 'category' => $r['category']);
				}
			}
    		break;

		default:
     		break;
	}
}
$smarty->assign_by_ref('res', $res);
$smarty->assign_by_ref('feedbacks', $feedbacks);
$smarty->assign_by_ref('filterGroup', $filterGroup);
$smarty->assign_by_ref('all_groups', $all_groups);

$smarty->assign('mid', 'tiki-list_object_permissions.tpl');
$smarty->display('tiki.tpl');
