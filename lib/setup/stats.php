<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/setup/stats.php,v 1.2.2.1 2007-11-04 22:08:34 nyloth Exp $
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],'tiki-setup.php')!=FALSE) {
  header('location: index.php');
  exit;
}

if ( $prefs['feature_referer_stats'] == 'y' ) {
    if ( isset($_SERVER['HTTP_REFERER']) ) {
        $pref = parse_url($_SERVER['HTTP_REFERER']);
        if ( isset($pref['host']) && !strstr($_SERVER['SERVER_NAME'], $pref['host']) ) {
            $tikilib->register_referer($pref['host']);
        }
    }
}

// Stats
if ( $prefs['feature_stats'] == 'y' ) {
	if ( $prefs['count_admin_pvs'] == 'y' || $user != 'admin' ) {
		if ( ! isset($section) or ( $section != 'chat' and $section != 'livesupport' ) ) {
			$tikilib->add_pageview();
		}
	}
}
