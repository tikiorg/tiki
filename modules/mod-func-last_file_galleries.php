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

function module_last_file_galleries_info() {
	return array(
		'name' => tra('Last-modified File Galleries'),
		'description' => tra('Displays the specified number of file galleries, starting from the most recently modified.'),
		'prefs' => array("feature_file_galleries"),
		'params' => array(),
		'common_params' => array('nonums', 'rows')
	);
}

function module_last_file_galleries( $mod_reference, $module_params ) {
	global $smarty, $prefs;
	$filegallib = TikiLib::lib('filegal');
	$ranking = $filegallib->get_files(0, $mod_reference["rows"], 'lastModif_desc', null, $prefs['fgal_root_id'], false, true, false, false);
	
	$smarty->assign('modLastFileGalleries', $ranking["data"]);
}
