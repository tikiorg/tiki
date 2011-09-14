<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

// get average server load in the last minute
$load = sys_getloadavg();
$server_load = $load[0];

if ( $prefs['use_load_threshold'] == 'y' and $tiki_p_access_closed_site != 'y' and !isset($bypass_siteclose_check) ) {
	if ( $server_load > $prefs['load_threshold'] ) {
		$url = 'tiki-error_simple.php?error=' . urlencode($prefs['site_busy_msg']);
		header('location: ' . $url);
		exit;
	}
}
$smarty->assign('server_load', $server_load == 0 ? '?' : $server_load);
