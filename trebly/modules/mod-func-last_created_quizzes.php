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

function module_last_created_quizzes_info() {
	return array(
		'name' => tra('Newest Quizzes'),
		'description' => tra('Displays the specified number of quizzes from newest to oldest.'),
		'prefs' => array("feature_quizzes"),
		'params' => array(),
		'common_params' => array('nonums', 'rows')
	);
}

function module_last_created_quizzes( $mod_reference, $module_params ) {
	global $tikilib, $smarty;
	$ranking = $tikilib->list_quizzes(0, $mod_reference["rows"], 'created_desc', '');
	
	$smarty->assign('modLastCreatedQuizzes', $ranking["data"]);
}
