<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-login.php,v 1.14 2003-08-07 04:33:57 rossta Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

# $Header: /cvsroot/tikiwiki/tiki/tiki-login.php,v 1.14 2003-08-07 04:33:57 rossta Exp $

// Initialization
require_once('tiki-setup.php');

/*
if (!isset($_REQUEST["login"])) {
  header("location: $HTTP_REFERER");
  die;  
}
*/

//Remember where user is logging in from and send them back later; using session variable for those of us who use WebISO services
if (!(isset($_SESSION['loginfrom']))) {
	if (isset($_SERVER['HTTP_REFERER'])) {
		$_SESSION['loginfrom'] = $_SERVER['HTTP_REFERER'];
	} else {
		//Oh well, back to tikiIndex
		$_SESSION['loginfrom'] = $tikiIndex;
	}
}

if ($tiki_p_admin == 'y') {
	if (isset($_REQUEST["su"])) {
		if ($userlib->user_exists($_REQUEST['username'])) {
			$_SESSION['user'] = $_REQUEST["username"];

			$smarty->assign_by_ref('user', $_REQUEST["username"]);
		}

		$url = $_SESSION['loginfrom'];
		//unset session variable for the next su
		unset($_SESSION['loginfrom']);
		header("location: $url");
		die;
	}
}

$https_mode = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on';
$https_login_required = $tikilib->get_preference('https_login_required', 'n');

if ($https_login_required == 'y' && !$https_mode) {
	$url = 'https://' . $https_domain;

	if ($https_port != 443)
		$url .= ':' . $https_port;

	$url .= $https_prefix . $tikiIndex;

	if (SID)
		$url .= '?' . SID;

	header("Location " . $url);
	exit;
}

$user = isset($_REQUEST['user']) ? $_REQUEST['user'] : false;
$pass = isset($_REQUEST['pass']) ? $_REQUEST['pass'] : false;
$challenge = isset($_REQUEST['challenge']) ? $_REQUEST['challenge'] : false;
$response = isset($_REQUEST['response']) ? $_REQUEST['response'] : false;
$isvalid = false;
$isdue = false;

// unneeded since admin/admin is created by tiki.sql; potential security hole
//if ($user == 'admin' && !$userlib->user_exists('admin')) {
//  if ($pass == 'admin') {
//     $isvalid = true;
//     $userlib->add_user('admin', 'admin', 'none');
//  }  
//} else {

// Verify user is valid
$isvalid = $userlib->validate_user($user, $pass, $challenge, $response);

// If the password is valid but it is due then force the user to change the password by
// sending the user to the new password change screen without letting him use tiki
// The user must re-nter the old password so no secutiry risk here
if ($isvalid) {
	$isdue = $userlib->is_due($user);
}
//}
if ($isvalid) {
	if ($isdue) {
		// Redirect the user to the screen where he must change his password.
		// Note that the user is not logged in he's just validated to change his password
		// The user must re-enter his old password so no secutiry risk involved
		$url = 'tiki-change_password.php?user=' . urlencode($user). '&amp;oldpass=' . urlencode($pass);
	} else {
		// User is valid and not due to change pass.. start session
		//session_register('user',$user);
		$_SESSION['user'] = $user;

		$smarty->assign_by_ref('user', $user);
		$url = $_SESSION['loginfrom'];
		//unset session variable in case user su's
		unset($_SESSION['loginfrom']);

		// Now if the remember me feature is on and the user checked the rememberme checkbox then ...
		if ($rememberme != 'disabled') {
			if (isset($_REQUEST['rme']) && $_REQUEST['rme'] == 'on') {
				$hash = $userlib->get_user_hash($_REQUEST['user']);

				setcookie('tiki-user', $hash, time() + $remembertime);
			}
		}
	}
} else {
	$url = 'tiki-error.php?error=' . urlencode(tra('Invalid username or password'));
}

if ($https_mode) {
	$stay_in_ssl_mode = isset($_REQUEST['stay_in_ssl_mode']) && $_REQUEST['stay_in_ssl_mode'] == 'on';

	if (!$stay_in_ssl_mode) {
		$http_domain = $tikilib->get_preference('http_domain', false);

		$http_port = $tikilib->get_preference('http_port', 80);
		$http_prefix = $tikilib->get_preference('http_prefix', '/');

		if ($http_domain) {
			$prefix = 'http://' . $http_domain;

			if ($http_port != 80)
				$prefix .= ':' . $http_port;

			$prefix .= $https_prefix;
			$url = $prefix . $url;

			if (SID)
				$url .= '?' . SID;
		}
	}
}

header('location: ' . $url);
exit;

?>