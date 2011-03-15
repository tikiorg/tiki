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

function module_search_box_info() {
	return array(
		'name' => tra('Search box'),
		'description' => tra('Small search form (for wiki, articles, blogs, etc.)'),
		'prefs' => array(), // feature_search_fulltext does not depend on feature_search
		'params' => array(
			'tiki' => array(
				'name' => tra('Tiki'),
				'description' => tra('If set to "y", the search performed is a "Tiki search".') . " " . tra('Default:') . ' "n"' . tra(' (full text search)')
			)
		)
	);
}

function module_search_box( $mod_reference, $module_params ) {
	global $smarty, $prefs;

	// Hack to deal with the two search types. If the requested search type is disabled but the other one is enabled, use it as a fallback.
	$smarty->assign('module_error', '');
	$type = (isset($module_params['tiki']) && $module_params['tiki'] == 'y') ? 'tiki' : 'fulltext';
	if ($prefs['feature_search'] == 'n' && $prefs['feature_search_fulltext'] == 'n') {
		$type = 'none';
		$smarty->assign('module_error', tra('Search is disabled.'));
	} elseif ($prefs['feature_search'] == 'n' && $type == 'tiki')
		$type = 'fulltext';
	elseif ($prefs['feature_search_fulltext'] == 'n' && $type == 'fulltext')
		$type = 'tiki';

	$smarty->assign('type', $type) ;
}
