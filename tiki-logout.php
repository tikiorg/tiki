<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-logout.php,v 1.9 2003-10-19 14:10:52 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
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

setcookie($user_cookie_site, '', -3600);
$userlib->user_logout($user);
session_unregister ('user');
unset ($_SESSION[$user_cookie_site]);
session_destroy();
unset ($user);
header ("location: $tikiIndex");
exit;

?>
