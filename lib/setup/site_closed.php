<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER['SCRIPT_NAME'], basename(__FILE__));

// Check to see if admin has closed the site
if ( $tiki_p_access_closed_site != 'y' and ! isset($bypass_siteclose_check) ) {
	global $base_url;
    $url = $base_url . 'tiki-error_simple.php?error=' . urlencode('' . $prefs['site_closed_msg']);
	header('Location: ' . $url);
	exit;
}

