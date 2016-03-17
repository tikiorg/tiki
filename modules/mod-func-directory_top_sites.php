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
function module_directory_top_sites_info()
{
	return array(
		'name' => tra('Top Directory Sites'),
		'description' => tra('Displays the specified number of the directory sites from most visited to least visited.'),
		'prefs' => array('feature_directory'),
		'params' => array(
			'desc' => array(
				'name' => tra('Show description'),
				'description' => tra('If set to "y", the description of the directory site appears.') . " " . tr('Default: "n".'),
				'filter' => 'word',
			),
			'maxdesc' => array (
				'name' => tra('Maximum length of description'),
				'description' => tra('If desc = "y", use maxdesc to set the maximum length of the directory site (in characters). Leave blank to set no maximum (show the entire description).') . " " . tr('Default: blank.'),
				'filter' => 'int',
			)
		),
		'common_params' => array('nonums')
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_directory_top_sites($mod_reference, $module_params)
{
	$tikilib = TikiLib::lib('tiki');
	$smarty = TikiLib::lib('smarty');
	$ranking = $tikilib->dir_list_all_valid_sites2(0, $mod_reference["rows"], 'hits_desc', '');

	$smarty->assign('desc', isset($module_params['desc']) ? $module_params['desc'] : 'n');	

	// only allow truncation if showing description
	if ($module_params['desc'] != 'n') {
		if ($module_params['maxdesc'] >= 1) {
			$smarty->assign('maxdesc', $module_params['maxdesc']);	
		}
	}
	
	$smarty->assign('modTopdirSites', $ranking["data"]);
}
