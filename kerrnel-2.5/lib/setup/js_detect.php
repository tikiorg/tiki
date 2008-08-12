<?php

// $Id$
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

// Javascript auto-detection
if ( isset($_SESSION['tiki_cookie_jar']) && isset($_SESSION['tiki_cookie_jar']['javascript_enabled']) ) {
	$prefs['javascript_enabled'] = $_SESSION['tiki_cookie_jar']['javascript_enabled'];
} else {
	// Set a session var to be able to generate non-javascript code if there is no javascript, when noscript tag is not useful enough
	$headerlib->add_js("setSessionVar('javascript_enabled','y');");
}

if ($prefs['pref_syntax'] == '1.9') {
	$javascript_enabled = $prefs['javascript_enabled'];
	$smarty->assign('javascript_enabled', $prefs['javascript_enabled']);
}

