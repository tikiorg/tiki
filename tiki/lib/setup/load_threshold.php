<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/setup/load_threshold.php,v 1.1 2007-10-06 15:18:45 nyloth Exp $
// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],'tiki-setup.php')!=FALSE) {
  header('location: index.php');
  exit;
}

// get average server load in the last minute
if ( $load = @file('/proc/loadavg') ) {
    list($server_load) = explode(' ', $load[0]);
    $smarty->assign('server_load', $server_load);
    if ( $use_load_threshold == 'y' and $tiki_p_access_closed_site != 'y' and !isset($bypass_siteclose_check) ) {
        if ( $server_load > $load_threshold ) {
            $url = 'tiki-error_simple.php?error=' . urlencode($site_busy_msg);
            header('location: ' . $url);
            exit;
        }
    }
} else {
	$smarty->assign('server_load', '?');
}
