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

function module_freetags_most_popular_info() {
	return array(
		'name' => tra('Most Popular Tags'),
		'description' => tra('Shows the most popular freetags. More popularity is indicated by a larger font.'),
		'prefs' => array( 'feature_freetags' ),
		'params' => array(
			'type' => array(
				'name' => tra('Display type'),
				'description' => tra('If set to "cloud", links are displayed as a cloud.') . " " . tr('Default: "list".'),
				'filter' => 'word'
			),
			'max' => array(
				'name' => tra('Maximum elements'),
				'description' => tra('If set to a number, limits the number of tags displayed.') . " " . tr('Default: 10.'),
				'filter' => 'int'
			)
		),
		'common_params' => array('rows') // This is not clean. We should use just max instead of max and rows as fallback,
	);
}

function module_freetags_most_popular( $mod_reference, $module_params ) {
	global $smarty;
	$globalperms = Perms::get();
	if ($globalperms->view_freetags) {
		global $freetaglib; require_once 'lib/freetag/freetaglib.php';
		$most_popular_tags = $freetaglib->get_most_popular_tags('', 0, empty($module_params['max']) ? $mod_reference["rows"] : $module_params['max']);
		$smarty->assign_by_ref('most_popular_tags', $most_popular_tags);
		$smarty->assign('type', (isset($module_params['type']) && $module_params['type'] == 'cloud') ? 'cloud' : 'list');
	}
}
