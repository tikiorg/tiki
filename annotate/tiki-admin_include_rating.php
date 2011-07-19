<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

global $ratingconfiglib; require_once 'lib/rating/configlib.php';
global $ratinglib; require_once 'lib/rating/ratinglib.php';

if( isset($_REQUEST['test']) && $access->is_machine_request() ) {
	$message = $ratinglib->test_formula( $_REQUEST['test'], array( 'type', 'object-id' ) );
	
	$access->output_serialized( array(
		'valid' => empty( $message ),
		'message' => $message,
	) );
	exit;
}

if( isset($_POST['create']) && ! empty( $_POST['name'] ) ) {
	$id = $ratingconfiglib->create_configuration( $_POST['name'] );
	$access->flash( tr('New configuration created (id %0)', $id) );
}

if( isset($_POST['edit']) ) {
	$ratingconfiglib->update_configuration( $_POST['config'], $_POST['name'], $_POST['expiry'], $_POST['formula'] );
	$access->flash( tra('Configuration updated.') );
}

$configurations = $ratingconfiglib->get_configurations();

$smarty->assign( 'configurations', $configurations );

