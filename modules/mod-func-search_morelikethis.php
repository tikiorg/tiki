<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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
	global $smarty;

	if ($object = current_object()) {
		$unifiedsearchlib = TikiLib::lib('unifiedsearch');

		$query = $unifiedsearchlib->buildQuery(array());
		$query->filterSimilar($object['type'], $object['object']);
		$query->setRange(0, $mod_reference['rows']);

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
