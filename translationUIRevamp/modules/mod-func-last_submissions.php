<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}


function module_last_submissions_info() {
	return array(
		'name' => tra('Last submissions'),
		'description' => tra('Lists the specified number of article submissions from newest to oldest.'),
		'prefs' => array("feature_submissions"),
		'params' => array(),
		'common_params' => array('nonums', 'rows')
	);
}

function module_last_submissions( $mod_reference, $module_params ) {
	global $tikilib, $smarty;
	$ranking = $tikilib->list_submissions(0, $mod_reference['rows'], 'created_desc', '', '');
	
	$smarty->assign('modLastSubmissions', $ranking["data"]);
}
