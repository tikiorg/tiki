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

function module_last_files_info() {
	return array(
		'name' => tra('Newest Files'),
		'description' => tra('List the specified number of files, starting from the most recently added.'),
		'prefs' => array("feature_file_galleries"),
		'params' => array(
			'galleryId' => array(
				'name' => tra('Gallery identifiers'),
				'description' => tra('If set to a set of file gallery identifiers, restricts the files to those in the identified galleries. The value is a colon-separated sequence of integers.') . " " . tra('Example value: 13, 2:13, 1:2:3:5:6.') . " " . tra('Not set by default.'),
				'filter' => 'int'
			),),
		'common_params' => array('nonums', 'rows')
	);
}

function module_last_files( $mod_reference, $module_params ) {
	global $smarty;
	$filegallib = TikiLib::lib('filegal');
	if (isset($module_params["galleryId"])) {
		if (strstr($module_params['galleryId'], ':')) {
			$ranking = $filegallib->get_files(0, $mod_reference["rows"], 'created_desc', '', explode(':',$module_params['galleryId']));
		} else {
			$ranking = $filegallib->get_files(0, $mod_reference["rows"], 'created_desc', '', $module_params["galleryId"]);
		}
	} else {
		global $prefs;
		$ranking = $filegallib->get_files(0, $mod_reference["rows"], 'created_desc', '', $prefs['fgal_root_id'], false, false, false, true, false, false, false, true);
	}
	
	$smarty->assign('modLastFiles', $ranking["data"]);
}
