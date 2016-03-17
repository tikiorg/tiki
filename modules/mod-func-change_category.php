<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/**
 * @return array
 */
function module_change_category_info()
{
	return array(
		'name' => tra('Change Category'),
		'description' => tra('Enables to categorize an object.') . " " . tra('This module currently only supports Wiki pages. Some combinations of Multiple categories, Detailed, Unassign and Assign may challenge intuition or be simply broken.'),
		'prefs' => array('feature_categories', 'feature_wiki'),
		'documentation' => 'Module change_category',
		'params' => array(
			'id' => array(
				'name' => tra('Category identifier'),
				'description' => tra('Changes the root of the displayed categories from default "TOP" to the category with the given identifier.') . " " . tra('Note that the root category is not displayed.') . " " . tra('Example value: 13.') . " " . tra('Defaults to 0 (root).'),
				'filter' => 'int',
				'profile_reference' => 'category',
			),
			'notop' => array(
				'name' => tra('No top'),
				'description' => tra('In non-detailed view, disallow uncategorizing. Example value: 1.') . " " . tra('Not set by default.'),
			),
			'path' => array(
				'name' => tra('Display path'),
				'description' => tra('Unless set to "n", display relative category paths in the category tree rather than category names.') . " " . tra('Paths are relative to the root category, which is not displayed.') . " " . tra('Example value:') . ' "n". ' . tra('Not set by default.'),
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
				'description' => tra('If set to "y", the module is not shown on pages which are not already categorized.') . " " . tra('Not set by default.'),
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
				'name' => tra('Group filter'),
				'description' => tra('Very particular filter option. If set to "y", only categories with a name matching one of the user\'s groups are shown, and descendants of these matching categories.') . " " . tra('Example values: y, n.') . " " . tra('Default value: n.'),
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

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_change_category($mod_reference, $module_params)
{
	global $prefs;
	$smarty = TikiLib::lib('tiki');
	$smarty = TikiLib::lib('smarty');
	$modlib = TikiLib::lib('mod');

	$smarty->assign('showmodule', false);

	$object = current_object();
	if ($object || $modlib->is_admin_mode(true)) {
		$categlib = TikiLib::lib('categ');

		if (!empty($module_params['id'])) {
			$id = $module_params['id'];
			$cat_parent = $categlib->get_category_name($id);
		} else {
			$id = 0;
			$cat_parent = '';
		}

		if (!empty($module_params['shy']) && !$modlib->is_admin_mode(true)) {
			$shy = $module_params['shy'] === 'y';
		} else {
			$shy = false;
		}

		$detailed = isset($module_params['detail']) ? $module_params['detail'] : "n";
		$smarty->assign('detailed', $detailed);

		$add = isset($module_params['add']) ? $module_params['add'] : "y";
		$smarty->assign('add', $add);

		$multiple = isset($module_params['multiple']) ? $module_params['multiple'] : "y";
		$smarty->assign('multiple', $multiple);


		$cat_type = $object['type'];
		$cat_objid = $object['object'];

		$categories = $categlib->getCategories($id ? array('identifier'=>$id, 'type'=>'descendants') : null);

		if (!empty($module_params['group']) && $module_params['group'] == 'y') {
			global $user;
			$userlib = TikiLib::lib('user');
			if (!$user) {
				return;
			}
			$userGroups = $userlib->get_user_groups_inclusion($user);
			foreach ($categories as $i => $cat) {
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
					unset($categories[$i]);
				}
			}
		}

		$managedCategories = array_keys($categories);
		if (isset($_REQUEST['remove']) && (!isset($module_params['del']) || $module_params['del'] != 'n')) {
			$originalCategories = $categlib->get_object_categories($cat_type, $cat_objid);
			// Check if the object is in the category to prevent infinite redirection.
			if (in_array($_REQUEST['remove'], $originalCategories) && in_array($_REQUEST['remove'], $managedCategories)) {
				$selectedCategories = array();
				$managedCategories = array_intersect(array((int) $_REQUEST['remove']), $managedCategories);
			}
		} elseif (isset($_REQUEST["modcatid"]) and $_REQUEST["modcatid"] == $id) {
			if (!isset($_REQUEST['modcatchange'])) {
				$selectedCategories = array();
			} elseif (is_array($_REQUEST['modcatchange'])) {
				$selectedCategories =  $_REQUEST['modcatchange'];
			} else {
				$selectedCategories = array($_REQUEST['modcatchange']);
			}
			foreach ($selectedCategories as &$selectedCategory) {
				$selectedCategory = (int) $selectedCategory;
			}
			if ($detailed != 'n') {
				$managedCategories = array_intersect($selectedCategories, $managedCategories);
			}
		}

		if (isset($selectedCategories)) {
			$objectperms = Perms::get(array('type' => $cat_type, 'object' => $cat_objid));
			if ($objectperms->modify_object_categories) {
				$categlib->update_object_categories($selectedCategories, $cat_objid, $cat_type, null, null, null, $managedCategories);
			}
			header('Location: '.$_SERVER['REQUEST_URI']);
			die;
		}

		$objectCategories = $categlib->get_object_categories($cat_type, $cat_objid);
		$isInAllManagedCategories = true;

		foreach ($categories as &$category) {
			if (in_array($category['categId'], $objectCategories)) {
				$category['incat'] = 'y';
				$shy = false;
			} else {
				$category['incat'] = 'n';
				$isInAllManagedCategories = false;
			}
		}
		if (count($categories) != 1) {
			unset($module_params['imgUrlNotIn']);
			unset($module_params['imgUrlIn']);
		}

		$smarty->assign('isInAllManagedCategories', $isInAllManagedCategories);
		$smarty->assign('showmodule', !$shy);
		$objectlib = TikiLib::lib('object');
		$title = $objectlib->get_title($cat_type, $cat_objid);
		if (empty($cat_parent)) {
			$smarty->assign('tpl_module_title', sprintf(tra('Categorize %s'), htmlspecialchars($title)));
		} else {
			$smarty->assign('tpl_module_title', sprintf(tra('Categorize %s in %s'), htmlspecialchars($title), htmlspecialchars($cat_parent)));
		}
		$smarty->assign('modcatlist', $categories);
		$smarty->assign('modcatid', $id);
	}
}
