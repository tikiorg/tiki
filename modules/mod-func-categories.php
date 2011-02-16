<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_categories_info() {
	return array(
		'name' => tra('Categories'),
		'description' => tra('Displays links to categories as a tree.'),
		'prefs' => array( 'feature_categories' ),
		'documentation' => 'Module categories',
		'params' => array(
			'type' => array(
				'name' => tra('Object type filter'),
				'description' => tra('Object type filter to apply when accessing a linked category. Example values:') . ' wiki page, article, faq, blog, image gallery, image, file gallery, tracker, trackerItem, quiz, poll, survey, sheet',
				'filter' => 'striptags'
			),
			'deep' => array(
				'name' => tra('Deep'),
				'description' => tra('Show subcategories objects when accessing a linked category. Possible values: on (default), off.'),
				'filter' => 'word'
			),
			'style' => array(
				'name' => tra('PHP Layers menu style'),
				'description' => tra('Sets the menu style if PHP Layers is enabled. Possible values: tree (default), vert, horiz, plain, phptree.'),
				'filter' => 'word'
			),
			'categId' => array(
				'name' => tra('Category ID'),
				'description' => tra('Limits displayed categories to a subtree of categories starting with the category with the given ID. Example value: 11. Default: 0 (don\'t limit display).'),
				'filter' => 'int'
			),
			'categParentIds' => array(
				'name' => tra('Show these categories and their children'),
				'description' => tra('Show only these categories and the immediate child categories of these. Example values: 3,5,6.'),
				'filter' => 'striptags'
			),
		),
	);
}

function module_categories( $mod_reference, &$module_params ) {
	global $smarty, $prefs;
	global $user;
	global $categlib; include_once ('lib/categories/categlib.php');
	if (isset($module_params['type'])) {
		$type = $module_params['type'];
		$urlEnd = '&amp;type='.urlencode($type);
	} else {
		$type = '';
		$urlEnd = '';
	}
	if (isset($module_params['deep']))
		$deep = $module_params['deep'];
	else
		$deep= 'on';
	$urlEnd .= "&amp;deep=$deep";
	$name = "";

	$categories = $categlib->get_all_categories_respect_perms(null, 'view_category');

	if ( empty($categories) ) {
		$module_params['error'] = tra("You do not have permission to use this feature");
	}
	if (isset($module_params['categId'])) {
		$categId = $module_params['categId'];
		foreach ($categories as $cat) {
			if ($cat['categId'] == $categId)
				$name = $cat['name'];
		}
	} else
		$categId = 0;
		
	if (isset($module_params['categParentIds'])) {
		$categParentIds = explode(',', $module_params['categParentIds']);
		$filtered_categories = array();
		foreach ($categories as $cat) {
			if (in_array($cat['categId'], $categParentIds) || in_array($cat['parentId'], $categParentIds) ) {
				$filtered_categories[] = $cat;
			}
		}
		$categories = $filtered_categories;
		unset($filtered_categories);
	}

	if (isset($module_params['style']))
		$style = $module_params['style'];
	else
		$style = 'tree';
		
	include_once ('lib/tree/categ_browse_tree.php');
	$tree_nodes = array();
	foreach ($categories as $cat) {
		$tree_nodes[] = array(
			"id" => $cat["categId"],
			"parent" => $cat["parentId"],
			"data" => '<a class="catname" href="tiki-browse_categories.php?parentId=' . $cat["categId"] .$urlEnd.'">' . $cat["name"] . '</a><br />'
		);
	}
	$tm = new CatBrowseTreeMaker("mod_categ");
	$res = $tm->make_tree($categId, $tree_nodes);
	$smarty->assign('tree', $res);

}
