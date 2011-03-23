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
				'description' => tra('Limit search results to a specific object type'),
				'filter' => 'text',
			),
		),
		'common_params' => array('rows'),
	);
}

function module_quick_search($mod_reference, $module_params)
{
	global $smarty;
	$prefill = array(
		'trigger' => false,
		'content' => '',
	);

	$moduleId = $mod_reference['moduleId'];
	if (isset($_SESSION['quick_search'][$moduleId])) {
		$prefill['trigger'] = true;
		$session = $_SESSION['quick_search'][$moduleId];

		if (isset($session['filter']['content'])) {
			$prefill['content'] = $session['filter']['content'];
		}
	}

	$smarty->assign('prefill', $prefill);
}

