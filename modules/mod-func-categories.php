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
function module_categories_info()
{
	return [
		'name' => tra('Categories'),
		'description' => tra('Displays links to categories as a tree.'),
		'prefs' => ['feature_categories'],
		'documentation' => 'Module categories',
		'params' => [
			'type' => [
				'name' => tra('Object type filter'),
				'description' => tra('Object type filter to apply when accessing a linked category. Example values:') . ' wiki page, article, faq, blog, image gallery, image, file gallery, tracker, trackerItem, quiz, poll, survey, sheet',
				'filter' => 'striptags',
			],
			'deep' => [
				'name' => tra('Deep'),
				'description' => tra('Show subcategories objects when accessing a linked category. Possible values: on (default), off.'),
				'filter' => 'word',
			],
			'categId' => [
				'name' => tra('Category ID'),
				'description' => tra('Limits displayed categories to a subtree of categories starting with the category with the given ID. Example value: 11. Default: 0 (don\'t limit display).'),
				'filter' => 'int',
				'profile_reference' => 'category',
			],
			'categParentIds' => [
				'name' => tra('Show these categories and their children'),
				'description' => tra('Show only these categories and the immediate child categories of these in the order the parameter specifies. Example values: 3,5,6.'),
				'filter' => 'striptags',
				'profile_reference' => 'category',
			],
			'selflink' => [
				'name' => tra('Category links to a page named as the category'),
				'description' => 'y|n .' . tra('If y, category links to a page named as the category'),
				'filter' => 'alpha',
			],
			'hideEmpty' => [
				'name' => tra('Hide Empty'),
				'description' => 'y|n .' . tra('If y, only categories with child objects will be shown.'),
				'filter' => 'alpha',
			],
			'onlyChildren' => [
				'name' => tra('Only Children'),
				'description' => 'y|n .' . tra('If y, only direct child categories will be shown. Default: n'),
				'filter' => 'alpha',
			],
			'customURL' => [
				'name' => tra('Custom URL'),
				'description' => tra('Custom URL for link to send you. Use %catId% as placeholder for catId. E.g. "ProductBrowse?categ=%catId%" '),
				'filter' => 'alpha',
			],
		],
	];
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_categories($mod_reference, &$module_params)
{
	global $prefs;
	global $user;
	$smarty = TikiLib::lib('smarty');
	$categlib = TikiLib::lib('categ');
	if (isset($module_params['type'])) {
		$type = $module_params['type'];
		$urlEnd = '&amp;type=' . urlencode($type);
	} else {
		$type = '';
		$urlEnd = '';
	}
	if (isset($module_params['deep'])) {
		$deep = $module_params['deep'];
	} else {
		$deep = 'on';
	}
	if ($deep === 'on') {
		$urlEnd .= "&amp;deep=$deep";
	}
	$name = "";


	if (isset($module_params['categId'])) {
		$categId = $module_params['categId'];
		if (isset($module_params['onlyChildren']) && $module_params['onlyChildren'] == 'y') {
			$categories = $categlib->getCategories(['identifier' => $categId, 'type' => 'children']);
		} else {
			$categories = $categlib->getCategories(['identifier' => $categId, 'type' => 'descendants']);
		}
		foreach ($categories as $cat) {
			if ($cat['categId'] == $categId) {
				$name = $cat['name'];
			}
		}
	} else {
		$categories = $categlib->getCategories();
		$categId = 0;
	}
	if (empty($categories)) {
		$smarty->clearAssign('tree');
		return;
	}

	if (isset($module_params['categParentIds'])) {
		$categParentIds = explode(',', $module_params['categParentIds']);
		$filtered_categories = [];
		foreach ($categParentIds as $c) {
			foreach ($categories as $cat) {
				if ($cat['categId'] == $c || $cat['parentId'] == $c) {
					$filtered_categories[] = $cat;
				}
			}
		}
		$categories = $filtered_categories;
		unset($filtered_categories);
	}

	include_once('lib/tree/BrowseTreeMaker.php');
	$tree_nodes = [];
	include_once('tiki-sefurl.php');
	foreach ($categories as $cat) {
		if (! empty($module_params['hideEmpty']) && $module_params['hideEmpty'] === 'y' && $cat['objects'] == 0) {
			$has_children = false;
			foreach ($cat['children'] as $child) {
				if (! empty($categories[$child]['objects'])) {
					$has_children = true;
					break;
				}
			}
			if (! $has_children) {
				continue;
			}
		}
		if (isset($module_params['selflink']) && $module_params['selflink'] == 'y') {
			$url = filter_out_sefurl('tiki-index.php?page=' . urlencode($cat['name']));
		} elseif (isset($module_params['customUrl'])) {
			$url = str_replace("%catId%", $cat['categId'], $module_params['customUrl']);
		} else {
			$url = filter_out_sefurl('tiki-browse_categories.php?parentId=' . $cat['categId'], 'category', $cat['name'], ! empty($urlEnd)) . $urlEnd;
		}
		$tree_nodes[] = [
			"id" => $cat["categId"],
			"parent" => $cat["parentId"],
			'parentId' => $cat['parentId'],
			'categId' => $cat['categId'],
			"data" => '<span style="float: left; cursor: pointer; visibility: hidden;" class="ui-icon ui-icon-triangle-1-e"></span><a class="catname" href="' . $url . '">' . htmlspecialchars($cat['name']) . '</a><br />'
		];
	}
	$res = '';
	$tm = new BrowseTreeMaker('mod_categ' . $mod_reference['position'] . $mod_reference['ord']);
	foreach ($categlib->findRoots($tree_nodes) as $node) {
		$res .= $tm->make_tree($node, $tree_nodes);
	}
	$smarty->assign('tree', $res);
}
