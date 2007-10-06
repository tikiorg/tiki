<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/setup/stats.php,v 1.1 2007-10-06 15:18:45 nyloth Exp $
// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],'tiki-setup.php')!=FALSE) {
  header('location: index.php');
  exit;
}

if ( $feature_referer_stats == 'y' ) {
    if ( isset($_SERVER['HTTP_REFERER']) ) {
        $pref = parse_url($_SERVER['HTTP_REFERER']);
        if ( isset($pref['host']) && !strstr($_SERVER['SERVER_NAME'], $pref['host']) ) {
            $tikilib->register_referer($pref['host']);
        }
    }
}

// Stats
if ( $feature_stats == 'y' ) {
	if ( $count_admin_pvs == 'y' || $user != 'admin' ) {
		if ( ! isset($section) or ( $section != 'chat' and $section != 'livesupport' ) ) {
			$tikilib->add_pageview();
		}
	}
}
