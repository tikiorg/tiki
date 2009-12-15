<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_top_visited_faqs_info() {
	return array(
		'name' => tra('Top Visited FAQs'),
		'description' => tra('Displays the specified number of FAQs with links to them, from the most visited one to the least.'),
		'prefs' => array( 'feature_faqs' ),
		'params' => array(),
		'common_params' => array('nonums', 'rows')
	);
}

function module_top_visited_faqs( $mod_reference, $module_params ) {
	global $tikilib, $smarty;
	$ranking = $tikilib->list_faqs(0, $mod_reference["rows"], 'hits_desc', '');
	
	$smarty->assign('modTopVisitedFaqs', $ranking["data"]);
}
