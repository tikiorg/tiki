<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER['SCRIPT_NAME'], basename(__FILE__));

/*
 * Check location for Tiki Integrator script and setup aux CSS file if needed by repository
 */
include_once('lib/integrator/integrator.php');
if ( (strpos($_SERVER['REQUEST_URI'], 'tiki-integrator.php') != 0) && isset($_REQUEST['repID']) ) {
	// Create instance of integrator
	$integrator = new TikiIntegrator($dbTiki);
	$headerlib->add_cssfile($integrator->get_rep_css($_REQUEST['repID']), 20);
}
