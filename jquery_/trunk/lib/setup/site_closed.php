<?php

// $Id$
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

// Check to see if admin has closed the site
if ( $tiki_p_access_closed_site != 'y' and ! isset($bypass_siteclose_check) ) {
    $url = 'tiki-error_simple.php?error=' . urlencode(''.$prefs['site_closed_msg']);
    header('location: '.$url);
    exit;
}

