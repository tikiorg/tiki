<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_last_tracker_comments_info() {
	return array(
		'name' => tra('Last tracker comments'),
		'description' => tra('Lists the specified number of tracker comments (optionally restricting to those in a specific tracker or tracker item) starting from the most recently posted.'),
		'prefs' => array( 'feature_trackers' ),
		'params' => array(
			'trackerId' => array(
				'name' => tra('Tracker identifier'),
				'description' => tra('If set to a tracker identifier, only displays the comments on the given tracker.') . " " . tra('Example value: 13.') . " " . tr('Not set by default.'),
				'filter' => 'int'
			),
			'itemId' => array(
				'name' => tra('Item identifier'),
				'description' => tra('If set to an item identifier, only displays the comments on the given item.') . " " . tra('Example value: 13.') . " " . tr('Not set by default.'),
				'filter' => 'int'
			)
		),
		'common_params' => array('rows', 'nonums')
	);
}

function module_last_tracker_comments( $mod_reference, $module_params ) {
	global $prefs, $smarty;
	
	$trackerId = isset($module_params["trackerId"]) ? $module_params["trackerId"] : 0;
	
	$itemId = isset($module_params["itemId"]) ? $module_params["itemId"] : 0;

	global $trklib;
	require_once ('lib/trackers/trackerlib.php');
		
	$ranking = $trklib->list_last_comments($trackerId, $itemId, 0, $mod_reference["rows"]);
	$smarty->assign('modLastModifComments', $ranking["data"]);
}
