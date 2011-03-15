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


function module_last_submissions_info() {
	return array(
		'name' => tra('Newest Article Submissions'),
		'description' => tra('Lists the specified number of article submissions from newest to oldest.'),
		'prefs' => array("feature_submissions"),
		'params' => array(),
		'common_params' => array('nonums', 'rows')
	);
}

function module_last_submissions( $mod_reference, $module_params ) {
	global $artlib, $smarty; require_once 'lib/articles/artlib.php';
	$ranking = $artlib->list_submissions(0, $mod_reference['rows'], 'created_desc', '', '');
	
	$smarty->assign('modLastSubmissions', $ranking["data"]);
}
