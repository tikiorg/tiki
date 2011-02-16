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

function module_breadcrumb_info() {
	return array(
		'name' => tra('Last visited pages'),
		'description' => tra('Displays the last Wiki pages visited by the user.'),
		'prefs' => array( 'feature_wiki' ),
		'documentation' => 'Module breadcrumb',
		'params' => array(
			'maxlen' => array(
				'name' => tra('Maximum length'),
				'description' => tra('Maximum number of characters in page names allowed before truncating.'),
				'filter' => 'int'
			),
		),
		'common_params' => array('nonums', 'rows')
	);
}

function module_breadcrumb( $mod_reference, $module_params ) {
	global $smarty, $prefs;
	global $categlib; include_once ('lib/categories/categlib.php');
	if (!isset($_SESSION["breadCrumb"])) {
		$_SESSION["breadCrumb"] = array();
	}

	if ($jail = $categlib->get_jail()) {
		global $objectlib; include_once ('lib/objectlib.php');//
		$objectIds=$objectlib->get_object_ids("wiki page", $_SESSION["breadCrumb"]);
	
		$breadIds=array();
		foreach($_SESSION["breadCrumb"] as $step) {
			if (isset($objectIds[$step])) $breadIds[$objectIds[$step]]=$step;
		}
		if ($breadIds) { // If we have visited pages and we're in a perspective
			$relevantIds=$categlib->filter_objects_categories(array_keys($breadIds), $jail);
		} else {
			$relevantIds=array_keys($breadIds);
		}

		$fullBreadCrumb=array();
		foreach ($breadIds as $breadId => $breadName) {
			if (in_array($breadId, $relevantIds)) $fullBreadCrumb[$breadId]=$breadName;
		}
	} else {
		$fullBreadCrumb=$_SESSION["breadCrumb"];
	}

	$bbreadCrumb = array_slice(array_reverse($fullBreadCrumb), 0, $mod_reference['rows']);
	$smarty->assign('breadCrumb', $bbreadCrumb);
	$smarty->assign('maxlen', isset($module_params["maxlen"]) ? $module_params["maxlen"] : 0);
}
