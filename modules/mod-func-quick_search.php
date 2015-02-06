<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * @return array
 */
function module_quick_search_info()
{
	return array(
		'name' => tra('Quick Search'),
		'description' => tra('Performs a search query and persists the results in the module for quick navigation access.'),
		'prefs' => array('feature_search'),
		'params' => array(
			'filter_type' => array(
				'name' => tra('Filter object type'),
				'description' => tra('Limit search results to a specific object type. Enter an object type to use a static filter or write "selector" to provide an input.'),
				'filter' => 'text',
			),
			'filter_category' => array(
				'name' => tra('Filter category'),
				'description' => tra('Limit search results to a specific category. Enter the comma separated list of category IDs to include in the selector. Single category will display no controls.'),
				'filter' => 'digits',
				'separator' => ',',
				'profile_reference' => 'category',
			),
		),
		'common_params' => array('rows'),
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_quick_search($mod_reference, $module_params)
{
	global $prefs;

	$smarty = TikiLib::lib('smarty');
	$unifiedsearchlib = TikiLib::lib('unifiedsearch');
	$categlib = TikiLib::lib('categ');

	$prefill = array(
		'trigger' => false,
		'content' => '',
		'type' => '',
		'categories' => '',
	);

	$types = null;
	$categories = array();

	if (isset ($module_params['filter_type'])) {
		if ($module_params['filter_type'] == 'selector') {
			$types = $unifiedsearchlib->getSupportedTypes();
		} else {
			$prefill['type'] = $module_params['filter_type'];
		}
	}

	if (isset ($module_params['filter_category']) && $prefs['feature_categories'] == 'y') {
		foreach ($module_params['filter_category'] as $categId) {
			if (Perms::get('category', $categId)->view_category) {
				$categories[$categId] = $categlib->get_category_name($categId);
			}
		}
	}

	$moduleId = $mod_reference['moduleId'];
	if (isset($_SESSION['quick_search'][$moduleId])) {
		$prefill['trigger'] = true;
		$session = $_SESSION['quick_search'][$moduleId];

		if (isset($session['filter']['content'])) {
			$prefill['content'] = $session['filter']['content'];
		}

		if (isset($session['filter']['type'])) {
			$prefill['type'] = $session['filter']['type'];
		}

		if (isset($session['filter']['categories'])) {
			$selected = $session['filter']['categories'];

			if (isset ($categories[$selected])) {
				$prefill['categories'] = $selected;
			}
		}
	}

	$smarty->assign('qs_prefill', $prefill);
	$smarty->assign('qs_categories', $categories);
	$smarty->assign('qs_all_categories', implode(' or ', array_keys($categories)));
	$smarty->assign('qs_types', $types);
}

