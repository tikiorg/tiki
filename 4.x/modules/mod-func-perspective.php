<?php
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_perspective_info() {
	return array(
		'name' => tra('Perspective'),
		'description' => tra('Enables to change current perspective.'),
		'prefs' => array( 'feature_perspective' ),
		'params' => array()
	);
}

function module_perspective( $mod_reference, $module_params ) {
	global $perspectivelib; require_once 'lib/perspectivelib.php';
	global $smarty;
	
	$perspectives = $perspectivelib->list_perspectives();
	$smarty->assign( 'perspectives', $perspectives );

	if( isset( $_SESSION['current_perspective'] ) ) {
		$smarty->assign( 'current_perspective', $_SESSION['current_perspective'] );
	} else {
		$smarty->assign( 'current_perspective', null );
	}
}
