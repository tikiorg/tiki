<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-login.php,v 1.46 2005-05-18 10:58:58 mose Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

# $Header: /cvsroot/tikiwiki/tiki/tiki-login.php,v 1.46 2005-05-18 10:58:58 mose Exp $

// Initialization
$bypass_siteclose_check = 'y';
require_once('tiki-setup.php');

if (!(isset($_REQUEST['user']) or isset($_REQUEST['username']))) {
	header("Location: tiki-login_scr.php");
	die;
}
// Alert user if cookies are switched off
if (ini_get('session.use_cookies') == 1) {
	if(!isset($_COOKIE['PHPSESSID'])) {
		$url = 'tiki-error.php?error=' . urlencode(tra('You have to enable cookies to be able to login to this site'));
		header("location: $url");
		die;
	}
}
	
//Remember where user is logging in from and send them back later; using session variable for those of us who use WebISO services
if (!(isset($_SESSION['loginfrom']))) {
	if (isset($_SERVER['HTTP_REFERER'])) {
//		$_SESSION['loginfrom'] = basename($_SERVER['HTTP_REFERER']);
		$_url = parse_url($_SERVER['HTTP_REFERER']);
		$_SESSION['loginfrom'] = $_url['path'];
		if (!empty($_url['query'])) {
			$_SESSION['loginfrom'] .= '?'.$_url['query'];
		}
	} else {
		//Oh well, back to tikiIndex
//		$_SESSION['loginfrom'] = basename($tikiIndex);
		$_url = parse_url($tikiIndex);
		$_SESSION['loginfrom'] = $_url['path'];
		if (!empty($_url['query'])) {
			$_SESSION['loginfrom'] .= '?'.$_url['query'];
		}
	}
}

if ($tiki_p_admin == 'y') {
	if (isset($_REQUEST["su"])) {
		if ($userlib->user_exists($_REQUEST['username'])) {
			$_SESSION["$user_cookie_site"] = $_REQUEST["username"];
			$smarty->assign_by_ref('user', $_REQUEST["username"]);
		}

		$url = $_SESSION['loginfrom'];
		//unset session variable for the next su
		unset($_SESSION['loginfrom']);
		header("location: $url");
		die;
	}
}

$https_mode = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on';
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

// Verify user is valid
$isvalid = $userlib->validate_user($user, $pass, $challenge, $response);

// If the password is valid but it is due then force the user to change the password by
// sending the user to the new password change screen without letting him use tiki
// The user must re-nter the old password so no security risk here
if ($isvalid) {
	$isdue = $userlib->is_due($user);
}
//}
if ($isvalid) {
	if ($isdue) {
		// Redirect the user to the screen where he must change his password.
		// Note that the user is not logged in he's just validated to change his password
		// The user must re-enter his old password so no security risk involved
		$url = 'tiki-change_password.php?user=' . urlencode($user). '&oldpass=' . urlencode($pass);
	} else {
		// User is valid and not due to change pass.. start session
		//session_register('user',$user);
		$_SESSION["$user_cookie_site"] = $user;

		$smarty->assign_by_ref('user', $user);
		$url = $_SESSION['loginfrom'];
		$logslib->add_log('login','logged from '.$url);
//	this code doesn't work
//                if (($url == $tikiIndex || substr($tikiIndex, strlen($tikiIndex)-strlen($url)-1) == '/'.$url) 
//		     && $useGroupHome == 'y') { /* go to the group page only if the loginfrom is the default page */
		if (($url == $tikiIndex || basename($url) == $tikiIndex || urldecode(basename($url)) == $tikiIndex || basename($url) == "tiki-login_scr.php" || $limitedGoGroupHome == "n") && $useGroupHome == 'y') { /* go to the group page only if the loginfrom is the default page */
			$groupHome = $userlib->get_user_default_homepage($user);
    			if ($groupHome) {
                    $url = strpos($groupHome,'http://')===0 ? $groupHome : "tiki-index.php?page=".$groupHome;
    			}
		}
		//unset session variable in case user su's
		unset($_SESSION['loginfrom']);

		// No sense in sending user to registration page
		// This happens if the user has just registered and it's first login
		if (preg_match("/tiki-register.php/",$url)) {
		    $url = preg_replace("/tiki-register.php*$/","tiki-index.php",$url);
		}

		// Now if the remember me feature is on and the user checked the rememberme checkbox then ...
		if ($rememberme != 'disabled') {
			if (isset($_REQUEST['rme']) && $_REQUEST['rme'] == 'on') {
				$hash = $userlib->get_user_hash($_REQUEST['user']);
				$cookie_path = $tikilib->get_preference('cookie_path', '/');
				$cookie_domain = $tikilib->get_preference('cookie_domain', $tikilib->get_preference('http_domain', $_SERVER['SERVER_NAME']));
				setcookie($user_cookie_site, $hash, time() + $remembertime, $cookie_path, $cookie_domain);
				$logslib->add_log('login',"got a cookie for $remembertime seconds");		
			}
		}
	}
} else {
	unset($user);
	unset($isvalid);
	$url = 'tiki-error.php?error=' . urlencode(tra('Invalid username or password'));
}

if ($https_mode) {
	$stay_in_ssl_mode = isset($_REQUEST['stay_in_ssl_mode']) && $_REQUEST['stay_in_ssl_mode'] == 'on';

	if (!$stay_in_ssl_mode) {
		$http_domain = $tikilib->get_preference('http_domain', '');

		$http_port = $tikilib->get_preference('http_port', 80);
		$http_prefix = $tikilib->get_preference('http_prefix', '/');

		$prefix = 'http://';

		if ($http_port != 80)
			$prefix .= ':' . $http_port;

		$prefix .= $http_domain;
		$url = $prefix . $url;

		if (SID)
			$url .= '?' . SID;
	}
}

if (isset($user) and $feature_score == 'y') {
	$tikilib->score_event($user, 'login');
}

if (isset($_REQUEST['page'])) {
  header('location: ' .  ${$_REQUEST['page']});
} else {
  header('location: ' . $url);
}
exit;

?>
