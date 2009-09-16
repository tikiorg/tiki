<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_featured_links_info() {
	return array(
		'name' => tra('Featured links'),
		'description' => tra('Displays the site\'s first featured links.'),
		'prefs' => array( 'feature_featuredLinks' ),
		'params' => array(),
		'common_params' => array('nonums', 'rows')
	);
}

function module_featured_links( $mod_reference, $module_params ) {
	global $tikilib, $smarty;
	
	$smarty->assign('featuredLinks', $tikilib->get_featured_links($mod_reference['rows']));
}
