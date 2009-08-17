<?php
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

global $perspectivelib; require_once 'lib/perspectivelib.php';
global $smarty;
global $prefs;

if( $prefs['feature_perspective'] == 'y' ) {
	$perspectives = $perspectivelib->list_perspectives();
	$smarty->assign( 'perspectives', $perspectives );

	if( isset( $_SESSION['current_perspective'] ) ) {
		$smarty->assign( 'current_perspective', $_SESSION['current_perspective'] );
	} else {
		$smarty->assign( 'current_perspective', null );
	}
}

?>
