<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once 'tiki-setup.php';
require_once 'lib/perspectivelib.php';

$access->check_feature( 'feature_perspective' );

// Force preference reload, new perspective will be taken in account.
$_SESSION['current_perspective'] = 0;
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

if( isset($_REQUEST['back']) && isset($_SERVER['HTTP_REFERER']) ) {
	header( 'Location: ' . $_SERVER['HTTP_REFERER'] );
} else {
	header( 'Location: tiki-index.php' );
}

// EOF
