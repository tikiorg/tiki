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

function module_top_objects_info() {
	return array(
		'name' => tra('Top Objects'),
		'description' => tra('Displays the specified number of objects, starting with the one having the most hits.'),
		'prefs' => array( 'feature_stats' ),
		'params' => array(),
		'common_params' => array('nonums', 'rows')
	);
}

function module_top_objects( $mod_reference, $module_params ) {
	global $smarty;
	global $statslib; include_once ('lib/stats/statslib.php');
	
	$best_objects_stats = $statslib->best_overall_object_stats($mod_reference["rows"]);
	
	$smarty->assign('modTopObjects', $best_objects_stats);
}
