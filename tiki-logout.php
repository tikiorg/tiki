<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// All Rights Reserved.
// See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE.
// See license.txt for details.
// $Id$

$bypass_siteclose_check = 'y';
require_once ('tiki-setup.php');
// go offline in Live Support
if ($prefs['feature_live_support'] == 'y') {
	include_once ('lib/live_support/lslib.php');
	if ($lslib->get_operator_status($user) != 'offline') {
		$lslib->set_operator_status($user, 'offline');
	}
}

setcookie($user_cookie_site, '', -3600, $cookie_path, $prefs['cookie_domain']);
$userlib->delete_user_cookie($user);
$userlib->user_logout($user);
$logslib->add_log('login', 'logged out');

if ( ($groupHome = $userlib->get_group_home('Anonymous')) != '' ) {
	$url = (preg_match('/^(\/|https?:)/', $groupHome)) ? $groupHome : 'tiki-index.php?page=' . $groupHome;
} else {
	$url = $prefs['site_tikiIndex'];
}
// RFC 2616 defines that the 'Location' HTTP headerconsists of an absolute URI
if ( !eregi('^https?\:', $url) ) {
	$url = (ereg('^/', $url) ? $url_scheme . '://' . $url_host . (($url_port != '') ? ":$url_port" : '') : $base_url) . $url;
}

if (SID) {
	$url.= '?' . SID;
}
if ( $prefs['auth_method'] == 'cas' && $user != 'admin' && $user != '' ) {
	require_once ('lib/phpcas/CAS.php');
	phpCAS::client($prefs['cas_version'], '' . $prefs['cas_hostname'], (int)$prefs['cas_port'], '' . $prefs['cas_path']);
	// Logout and come back to the URL as Anonymous
	// Pass URL AND Service as we don't know how the server will react
	phpCAS::logoutWithRedirectServiceAndUrl($url,$url);
}

session_unregister('user');
unset($_SESSION[$user_cookie_site]);
session_destroy();
/* change group home page or desactivate if no page is set */
header('Location: ' . $url);
exit;
