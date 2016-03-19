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
function module_search_morelikethis_info()
{
	return array(
		'name' => tra('More Like This'),
		'description' => tra('Uses the unified search to provide similar documents.'),
		'prefs' => array('feature_search'),
		'params' => array(
			'typefilters' => array(
				'required' => false,
				'name' => tra('Object Type Filters'),
				'description' => tra('Comma-separated types to allow.'),
				'filter' => 'text',
			),
			'textfilters' => array(
				'required' => false,
				'name' => tra('Text Search Filters'),
				'description' => tra('Comma-separated text search filters to use. Use "=" to separate field and value.'),
				'filter' => 'text',
			),
		),
		'common_params' => array('nonums', 'rows')
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_search_morelikethis($mod_reference, $module_params)
{
	global $prefs;

	$smarty = TikiLib::lib('smarty');

	$textfilters = array();
	$typefilters = array();
	if (!empty($module_params['textfilters'])) {
		$filters = explode(",", $module_params['textfilters']);
		$filters = array_map('trim', $filters);
		foreach($filters as $f) {
			$exploded = explode("=", $f);
			if (!empty($exploded[1]) && !empty($exploded[0])) {
				$textfilters[$exploded[0]] = $exploded[1];
			}
		}
	}
	if (!empty($module_params['typefilters'])) {
		$typefilters = explode(",", $module_params['typefilters']);
		$typefilters = array_map('trim', $typefilters);
	}

	if ($object = current_object()) {
		$unifiedsearchlib = TikiLib::lib('unifiedsearch');

		$query = $unifiedsearchlib->buildQuery(array());
		$query->filterSimilar($object['type'], $object['object']);
		$smarty->assign('simobject', $object);
		$query->setRange(0, $mod_reference['rows']);
		foreach ($textfilters as $k => $v) { 
			$query->filterContent($v, $k);
		}
		if (!empty($typefilters)) {
			$query->filterType($typefilters);
		}

		if ($prefs['federated_enabled'] == 'y') {
			$fed = TikiLib::lib('federatedsearch');
			$fed->augmentSimilarQuery($query, $object['type'], $object['object']);
		}

		try {
			$morelikethis = $query->search($unifiedsearchlib->getIndex());

			$smarty->assign('modMoreLikeThis', $morelikethis);
			$smarty->assign('module_rows', $mod_reference["rows"]);
		} catch (Search_Elastic_NotFoundException $e) {
			// Target document not found - excluded from index, ignore module
		}
	}

	$smarty->assign('tpl_module_title', tra("Similar pages"));
}
