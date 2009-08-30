<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_freetags_morelikethis_info() {
	return array(
		'name' => tra('Similar freetags'),
		'description' => tra('Shows wiki pages with similar freetags.') . ' Warning: the determination of similarity may not behave as you would expect.',
		'prefs' => array( 'feature_freetags', 'feature_wiki' ),
		'params' => array(),
		'common_params' => array('nonums', 'rows')
	);
}

function module_freetags_morelikethis( $mod_reference, $module_params ) {
	global $page;
	global $smarty;
	global $freetaglib;
	include_once 'lib/freetag/freetaglib.php';
	
	$globalperms = Perms::get();
	if( ! empty( $page ) && $globalperms->view ) {
		$morelikethis = $freetaglib->get_similar( 'wiki page', $page, $mod_reference["rows"] );
		$smarty->assign('modMoreLikeThis', $morelikethis);
		$smarty->assign('module_rows', $mod_reference["rows"]);
	}
	$smarty->assign('tpl_module_title', tra("Similar pages"));
}