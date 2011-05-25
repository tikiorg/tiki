<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_change_category_info() {
	return array(
		'name' => tra('Change Category'),
		'description' => tra('Enables to categorize an object.') . ' This module currently only supports Wiki pages. Some combinations of Multiple categories, Detailed, Unassign and Assign may challenge intuition or be simply broken.',
		'prefs' => array( 'feature_categories', 'feature_wiki' ),
		'documentation' => 'Module change_category',
		'params' => array(
			'id' => array(
				'name' => tra('Category identifier'),
				'description' => tra('Changes the root of the displayed categories from default "TOP" to the category with the given identifier.') . " " . tra('Note that the root category is not displayed.') . " " . tra('Example value: 13.') . " " . tra('Defaults to 0 (root).'),
				'filter' => 'int'
			),
			'notop' => array(
				'name' => tra('No top'),
				'description' => tra('In non-detailed view, disallow uncategorizing. Example value: 1.') . " " . tr('Not set by default.')
			),
			'path' => array(
				'name' => tra('Display path'),
				'description' => tra('Instead of category names, diplay their path in the category tree starting from the category root defined.') . " " . tra('Note that the root category is not displayed.') . " " . tra('Example value: 1.') . " " . tra('Not set by default.'),
			),
			'multiple' => array(
				'name' => tra('Multiple categories'),
				'description' => tra('If set to "n", only allows categorizing in one category (from those displayed).') . " " . tra('Not set by default.'),
			),
			'categorize' => array(
				'name' => tra('Categorize'),
				'description' => tra('String to display on the button to submit new categories, when multiple categories is enabled. Default value: Categorize.'),
			),
			'shy' => array(
				'name' => tra('Shy'),
				'description' => tra('If set to "y", the module is not shown on pages which are not already categorized.' . " " . tra('Not set by default.')),
			),
			'detail' => array(
				'name' => tra('Detailed'),
				'description' => tra('If set to "y", shows a list of categories in which the object is. If deletion is not disabled, it is done with the list.') . " " . tra('Not set by default.'),
			),
			'del' => array(
				'name' => tra('Unassign'),
				'description' => tra('If set to "n", the detailed list of categories will not offer to unassign a category.') . " " . tra('Not set by default.'),
				'depends' => 'detail'
			),
			'add' => array(
				'name' => tra('Assign'),
				'description' => tra('If set to "y", allow to assign new categories.') . " " . tra('Example values: y, n.') . " " . tra('Default value: y.'),
			),
			'group' => array(
				'name' => 'Group filter',
				'description' => 'Very particular filter option. If set to "y", only categories with a name matching one of the user\'s groups are shown, and descendants of these matching categories.' . " " . tra('Example values: y, n.') . " " . tra('Default value: n.'),
			),
			'imgUrlNotIn' => array(
				'name' => tra('Image URL not in category'),
				'description' => tra('Very particular parameter. If both this and "Image URL in category" are set and the root category contains a single child category, the module only displays an image with this URL if the object is not in the category.') . ' ' . tra('Example value:') . ' http://www.organization.org/img/redcross.png.',
			),
			'imgUrlIn' => array(
				'name' => tra('Image URL in category'),
				'description' => tra('Very particular parameter. If both this and "Image URL not in category" are set and the root category contains a single child category, the module only displays an image with this URL if the object is in the category.') . ' ' . tra('Example value:') . ' http://www.organization.org/img/bigplus.png.',
			),
		),
	);
}

function module_change_category( $mod_reference, $module_params ) {
	global $prefs, $tikilib, $smarty;
	global $categlib; require_once('lib/categories/categlib.php');
	
	// temporary limitation to wiki pages
	if (!empty($_REQUEST['page']) || !empty($_REQUEST['page_ref_if'])) {
		if (empty($_REQUEST['page'])) {
			global $structlib; include_once('lib/structures/structlib.php');
			$page_info = $structlib->s_get_page_info($_REQUEST['page_ref_id']);
			$_REQUEST['page'] = $page_info['page'];
		}
		if (!empty($module_params['id'])) {
			$id = $module_params['id'];
			$cat_parent = $categlib->get_category_name($id);
		} else {
			$id = 0;
			$cat_parent = '';
		}
	
		$shy = isset($module_params['shy']);
	
		$detailed = isset($module_params['detail']) ? $module_params['detail'] : "n";
		$smarty->assign('detailed', $detailed);
	
		$add = isset($module_params['add']) ? $module_params['add'] : "y";
		$smarty->assign('add', $add);
	
		$multiple = isset($module_params['multiple']) ? $module_params['multiple'] : "y";
		$smarty->assign('multiple', $multiple);
	
	
		$cat_type = 'wiki page';
		$cat_objid = $_REQUEST['page'];
		
		$categs = $categlib->list_categs($id);
	
		if (!empty($module_params['group']) && $module_params['group'] == 'y') {
			global $userlib, $user;
			if (!$user) {
				return;
			}
			$userGroups = $userlib->get_user_groups_inclusion($user);
			foreach ($categs as $i=>$cat) {
				if (isset($userGroups[$cat['name']])) {
					continue;
				}
				$ok = false;
				foreach ($cat['tepath'] as $c) {
					if (isset($userGroups[$c])) {
						$ok = true;
						break;
					}
				}
				if (!$ok) {
					unset($categs[$i]);
				}
			}
		}
	
		if (empty($categs)) {
			return;
		}
	
		$categsid = array();
		foreach ($categs as $categ) {
			$categsid[] = $categ['categId'];
		}

		$unassignedCategs = array();
		$assignedCategs = array();
		if (isset($_REQUEST['remove']) && in_array($_REQUEST['remove'], $categsid) && (!isset($module_params['del']) || $module_params['del'] != 'n')) {
			$oldCategs = $categlib->get_object_categories($cat_type, $cat_objid);
			if (in_array($_REQUEST['remove'], $oldCategs)) {
				$unassignedCategs[] = (int)$_REQUEST['remove'];
			}
		} elseif (isset($_REQUEST["modcatid"]) and $_REQUEST["modcatid"] == $id) {
			$newCategs = is_array($_REQUEST['modcatchange']) ? $_REQUEST['modcatchange'] : array($_REQUEST['modcatchange']);
			foreach($newCategs as &$newCateg)
				$newCateg = (int) $newCateg;
			$oldCategs = $categlib->get_object_categories($cat_type, $cat_objid);

			if ($detailed == 'n') 
				$unassignedCategs = array_diff(array_intersect($oldCategs, $categsid), $newCategs);
			if (isset($_REQUEST['modcatchange'])) 
				$assignedCategs = array_diff($newCategs, $oldCategs);
		}

		if (!empty($assignedCategs) || !empty($unassignedCategs)) {
			$objectperms = Perms::get( array( 'type' => $cat_type, 'object' => $cat_objid ) );
			if ($objectperms->modify_object_categories) {
				$assignedCategs = Perms::filter( array( 'type' => 'category' ), 'object', $assignedCategs, array( 'object' => 'category' ), 'add_object' );

				$categlib->categorize_page($cat_objid, $assignedCategs);
				if ($catObjectId = $categlib->is_categorized($cat_type, $cat_objid)) {
					$categlib->remove_object_from_categories($catObjectId, Perms::filter( array( 'type' => 'category' ), 'object', $unassignedCategs, array( 'object' => 'category' ), 'remove_object' ));
				}
			}
			header('Location: '.$_SERVER['REQUEST_URI']);
			die;
		}

		$incategs = $categlib->get_object_categories($cat_type, $cat_objid);
		$remainCateg = false;
		$modcatlist = array();
		$visibleCategs = Perms::filter( array( 'type' => 'category' ), 'object', $categsid, array( 'object' => 'category' ), 'view_category' );

		$indexedCategs = array();
		foreach ($categs as $categ) 
			$indexedCategs[$categ['categId']] = $categ;

		foreach ($visibleCategs as $categId) {
			$modcatlist[$categId] = $indexedCategs[$categId];
			if (in_array($categId,$incategs)) {
				$modcatlist[$categId]['incat'] = 'y';
				$shy = false;
			} else {
				$modcatlist[$categId]['incat'] = 'n';
				$remainCateg = true;
			}
		}
		if (count($modcatlist) != 1) {
			unset($module_params['imgUrlNotIn']);
			unset($module_params['imgUrlIn']);
		}
	
		$smarty->assign_by_ref('remainCateg', $remainCateg);
		$smarty->assign('showmodule',!$shy);
		if (empty($cat_parent))
			$smarty->assign('tpl_module_title',sprintf(tra('Categorize %s'), htmlspecialchars($_REQUEST['page'])));
		else
			$smarty->assign('tpl_module_title',sprintf(tra('Categorize %s in %s'), htmlspecialchars($_REQUEST['page']), htmlspecialchars($cat_parent)));
		$smarty->assign('modcatlist',$modcatlist);
		$smarty->assign('modcatid',$id);
	}
}
