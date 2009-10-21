<?php

require_once 'tiki-setup.php';
require_once 'lib/perspectivelib.php';

$access->check_feature( 'feature_perspective' );

// Force preference reload, new perspective will be taken in account.
unset($_SESSION['current_perspective']);
$_SESSION['need_reload_prefs'] = true;

if( isset($_REQUEST['perspective']) ) {
	$perspective = $_REQUEST['perspective'];
	if( $perspectivelib->perspective_exists( $perspective ) ) {
		foreach( $perspectivelib->get_domain_map() as $domain => $persp ) {
			if( $persp == $perspective ) {
				$targetUrl = 'http://' . $domain;

				header( 'Location: ' . $targetUrl );
				exit;
			}
		}

		$_SESSION['current_perspective'] = $perspective;
	}
}

header( 'Location: tiki-index.php' );

// EOF
