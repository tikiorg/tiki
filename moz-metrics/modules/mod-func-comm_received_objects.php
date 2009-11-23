<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_comm_received_objects_info() {
	return array(
		'name' => tra('Received objects'),
		'description' => tra('Displays the number of pages received (via Communications).'),
		'prefs' => array("feature_comm"),
		'params' => array()
	);
}

function module_comm_received_objects( $mod_reference, $module_params ) {
	global $tikilib, $smarty;
	
	$ranking = $tikilib->list_received_pages(0, -1, 'pageName_asc');
	
	$smarty->assign('modReceivedPages', $ranking["cant"]);
}
