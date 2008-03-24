<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/setup/integrator.php,v 1.1.2.1 2007-11-04 22:08:34 nyloth Exp $
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],'tiki-setup.php')!=FALSE) {
  header('location: index.php');
  exit;
}

/*
 * Check location for Tiki Integrator script and setup aux CSS file if needed by repository
 */
include_once('lib/integrator/integrator.php');
if ( (strpos($_SERVER['REQUEST_URI'], 'tiki-integrator.php') != 0) && isset($_REQUEST['repID']) ) {
	// Create instance of integrator
	$integrator = new TikiIntegrator($dbTiki);
	$headerlib->add_cssfile($integrator->get_rep_css($_REQUEST['repID']), 20);
}
