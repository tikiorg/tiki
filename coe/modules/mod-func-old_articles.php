<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_old_articles_info() {
	return array(
		'name' => tra('Old articles'),
		'description' => tra('Displays the specified number of old articles (which do not show on articles home page anymore).'),
		'prefs' => array( 'feature_articles' ),
		'params' => array(),
		'common_params' => array("rows", "nonums")
	);
}

function module_old_articles( $mod_reference, $module_params ) {
	global $user, $prefs, $tikilib, $smarty;
	if (!isset($prefs['maxArticles']))
		$prefs['maxArticles'] = 0;
	
	$ranking = $tikilib->list_articles($prefs['maxArticles'], $mod_reference["rows"], 'publishDate_desc', '', '', '', $user);
	$smarty->assign('modOldArticles', $ranking["data"]);
}