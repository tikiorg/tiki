<?php

// $Id$
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

// get average server load in the last minute
if ( is_readable('/proc/loadavg') && ($load = file('/proc/loadavg')) ) {
    list($server_load) = explode(' ', $load[0]);
    $smarty->assign('server_load', $server_load);
    if ( $prefs['use_load_threshold'] == 'y' and $tiki_p_access_closed_site != 'y' and !isset($bypass_siteclose_check) ) {
        if ( $server_load > $prefs['load_threshold'] ) {
            $url = 'tiki-error_simple.php?error=' . urlencode($prefs['site_busy_msg']);
            header('location: ' . $url);
            exit;
        }
    }
} else {
	$smarty->assign('server_load', '?');
}
