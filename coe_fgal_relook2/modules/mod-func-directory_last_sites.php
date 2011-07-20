<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_directory_last_sites_info() {
	return array(
		'name' => tra('Newest Directory Sites'),
		'description' => tra('Displays the specified number of the directory sites most recently added.'),
		'prefs' => array( 'feature_directory' ),
		'params' => array(
			'absurl' => array(
				'name' => tra('Absolute URL'),
				'description' => tra('If set to "y", some of the links use an absolute URL instead of a relative one. This can avoid broken links if the module is to be sent in a newsletter, for example.') . " " . tr('Default: "n".')
			),
			'categoryId' => array(
				'name' => tra('Directory category identifier'),
				'description' => tra('If set to a directory category identifier, only displays the sites in the specified directory category.') . " " . tr('Not set by default.')
			),
			'more' => array(
				'name' => tra('More'),
				'description' => tra('If set to "y", displays a button labelled "More" that links to the directory.') . " " . tr('Not set by default.')
			),
			'desc' => array(
				'name' => tra('Show description'),
				'description' => tra('If set to "y", the description of the directory site appears.') . " " . tr('Default: "n".'),
				'filter' => 'word'
			),
			'maxdesc' => array (
				'name' => tra('Maximum length of description'),
				'description' => tra('If desc = "y", use maxdesc to set the maximum length of the directory site (in characters). Leave blank to set no maximum (show the entire description).') . " " . tr('Default: blank.'),
				'filter' => 'int'
			)
			
		),
		'common_params' => array('nonums', 'rows')
	);
}

function module_directory_last_sites( $mod_reference, $module_params ) {
	global $prefs, $tikilib, $smarty;
	
	if (isset($module_params['categoryId'])) {
		global $dirlib; include_once('lib/directory/dirlib.php');
		$ranking = $dirlib->dir_list_sites($module_params['categoryId'], 0, $mod_reference["rows"]);
	} else
		$ranking = $tikilib->dir_list_all_valid_sites2(0, $mod_reference["rows"], 'created_desc', '');
	$smarty->assign('modLastdirSites', $ranking["data"]);
	$smarty->assign('absurl', isset($module_params["absurl"]) ? $module_params["absurl"] : 'n');
	
		$smarty->assign('desc', isset($module_params['desc']) ? $module_params['desc'] : 'n');	

	// only allow truncation if showing description
	if ($module_params['desc'] != 'n'){
		if ($module_params['maxdesc'] >= 1){
			$smarty->assign('maxdesc', $module_params['maxdesc']);	
		}
	}

}
