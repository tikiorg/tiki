<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_num_submissions_info() {
	return array(
		'name' => tra('Waiting Submissions'),
		'description' => tra('Displays the number of article submissions waiting examination and a link to the list.'),
		'prefs' => array( 'feature_submissions' ),
		'params' => array()
	);
}

function module_num_submissions( $mod_reference, $module_params ) {
	global $tikilib, $smarty;
	$ranking = $tikilib->list_submissions(0, -1, 'created_desc', '', '');
	
	$smarty->assign('modNumSubmissions', $ranking["cant"]);
}
