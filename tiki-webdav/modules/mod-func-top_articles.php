<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_top_articles_info() {
	return array(
		'name' => tra('Top articles'),
		'description' => tra('Lists the specified number of articles with links to them, from the most visited one to the least.'),
		'prefs' => array( 'feature_articles' ),
		'params' => array(),
		'common_params' => array('nonums', 'rows')
	);
}

function module_top_articles( $mod_reference, $module_params ) {
	global $tikilib, $smarty, $user;
	$ranking = $tikilib->list_articles(0, $mod_reference['rows'], 'nbreads_desc', '', '', '', $user);
	
	$smarty->assign('modTopArticles', $ranking["data"]);
}
