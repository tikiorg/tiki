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
function module_search_wiki_page_info()
{
	return array(
		'name' => tra('Search For a Wiki Page'),
		'description' => tra('Search for a wiki page by name.') . ' ' . tra('Deprecated - use the Search module instead'), // Search doesn't support Exact Match
		'prefs' => array('feature_wiki'),
		'params' => array(
			'exact' => array(
				'name' => tra('Exact Match'),
				'description' => tra('Exact match checkbox checked by default if set to "y".') . " " . tr('Default: "n".'),
				'filter' => 'alpha'
			),
		),
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_search_wiki_page($mod_reference, $module_params)
{
	$smarty = TikiLib::lib('smarty');
	$request = $smarty->getTemplateVars('exact_match');
	if (isset($request)) {
		$smarty->assign('exact', $request);
	} else {
		$smarty->assign('exact', isset($module_params['exact']) ? $module_params['exact'] : 'n');
	}
}
