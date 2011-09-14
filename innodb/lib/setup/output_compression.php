<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

// no compression at all
$smarty->assign('gzip', 'Disabled');
$smarty->assign('gzip_handler', 'none');

if ( ! empty($force_no_compression) && $force_no_compression ) {
	ini_set('zlib.output_compression', 'off');
} else {
	// php compression enabled?
	if ( ini_get('zlib.output_compression') == 1 ) {
		$smarty->assign('gzip', 'Enabled');
		$smarty->assign('gzip_handler', 'php');
	// if not, check if tiki compression is enabled
	} elseif ( $prefs['feature_obzip'] == 'y' ) {
		// tiki compression is enabled, then let activate the handler
		ob_start('ob_gzhandler');
		$smarty->assign('gzip_handler', 'tiki');
		$smarty->assign('gzip', 'Enabled');
	}
} 
