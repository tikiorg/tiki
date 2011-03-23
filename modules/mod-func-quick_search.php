<?php

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
			),
		),
		'common_params' => array('rows'),
	);
}

function module_quick_search($mod_reference, $module_params)
{
	$smarty = TikiLib::lib('smarty');
	$unifiedsearchlib = TikiLib::lib('unifiedsearch');

	$prefill = array(
		'trigger' => false,
		'content' => '',
		'type' => '',
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
	}

	$smarty->assign('qs_prefill', $prefill);
	$smarty->assign('qs_types', $types);
}

