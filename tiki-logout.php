<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-logout.php,v 1.14 2004-06-09 20:13:06 teedog Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$bypass_siteclose_check = 'y';
require_once ('tiki-setup.php');

// go offline in Live Support
if ($feature_live_support == 'y') {
	include_once ('lib/live_support/lslib.php');

	if ($lslib->get_operator_status($user) != 'offline') {
		$lslib->set_operator_status($user, 'offline');
	}
}

$cookie_path = $tikilib->get_preference('cookie_path', '/');
$cookie_domain = $tikilib->get_preference('cookie_domain', $tikilib->get_preference('http_domain', $_SERVER['SERVER_NAME']));
setcookie($user_cookie_site, '', -3600, $cookie_path, $cookie_domain);
$userlib->user_logout($user);
$logslib->add_log('login','logged out');		
session_unregister ('user');
unset ($_SESSION[$user_cookie_site]);
session_destroy();
unset ($user);
$tikiIndex = $tikilib->get_preference("tikiIndex",'tiki-index.php'); /* desactive group home page */
if ($tikilib->get_preference('auth_method', 'tiki') == 'cas') {
	$userlib->user_logout_cas();
}
header ("location: $tikiIndex");
exit;

?>
