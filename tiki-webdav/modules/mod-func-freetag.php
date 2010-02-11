<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_freetag_info() {
	return array(
		'name' => tra('Freetags editor'),
		'description' => tra('Shows current freetags and enables to add and remove some if permissions allow.'),
		'prefs' => array( 'feature_freetags' ),
		'params' => array()
	);
}

function module_freetag( $mod_reference, $module_params ) {
	global $sections, $section;
	global $smarty;
	
	$globalperms = Perms::get();
	if ($globalperms->view_freetags && isset($sections[$section])) {
		$tagid = 0;
		$par = $sections[$section];
		if (isset($par['itemkey']) && !empty($_REQUEST["{$par['itemkey']}"])) {
			$tagid = $_REQUEST["{$par['itemkey']}"];
		} elseif (isset($par['key']) && !empty($_REQUEST["{$par['key']}"])) {
			$tagid = $_REQUEST["{$par['key']}"];
		}
		if ($tagid)
			$smarty->assign('viewTags', 'y');
	}
}
