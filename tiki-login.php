<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-login.php,v 1.57 2006-12-21 14:57:56 mose Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

# $Header: /cvsroot/tikiwiki/tiki/tiki-login.php,v 1.57 2006-12-21 14:57:56 mose Exp $

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

if ($feature_intertiki == 'y') {
  if (strstr($user,'@') && empty($feature_intertiki_mymaster)) {
    $_REQUEST['intertiki'] = substr($user,strpos($user,'@')+1);
    $user = substr($user,0,strpos($user,'@'));
  } elseif (!empty($feature_intertiki_mymaster)) {
    $_REQUEST['intertiki'] = $feature_intertiki_mymaster;
  }
}

if ($user == 'admin') {
    // admin is always local
    $feature_intertiki = 'n';
    unset($_REQUEST['intertiki']);
}


if ($feature_intertiki == 'y' and isset($_REQUEST['intertiki']) and in_array($_REQUEST['intertiki'],array_keys($interlist)) and $user and $pass) {

    include_once('XML/RPC.php');

    function intervalidate($remote,$user,$pass,$get_info = false) {
	global $tiki_key;
	$remote['path'] = preg_replace("/^\/?/","/",$remote['path']);
	$client = new XML_RPC_Client($remote['path'], $remote['host'], $remote['port']);
	$client->setDebug(0);
	$msg = new XML_RPC_Message(
				   'intertiki.validate',
				   array(
					 new XML_RPC_Value($tiki_key, 'string'),
					 new XML_RPC_Value($user, 'string'),
					 new XML_RPC_Value($pass, 'string'),
					 new XML_RPC_Value($get_info, 'boolean')
					 ));
	$result = $client->send($msg);
	return $result;
    }

    $rpcauth = intervalidate($interlist[$_REQUEST['intertiki']],$user,$pass,!empty($feature_intertiki_mymaster)? true : false);

    if (!$rpcauth) {
	$logslib->add_log('login','intertiki : '.$user.'@'.$_REQUEST['intertiki'].': Failed');
	$smarty->assign('msg',tra('Unable to contact remote server.'));
	$smarty->display('error.tpl');
	exit;
    } else {
	if ($faultCode = $rpcauth->faultCode()) {
	    if ($faultCode == 102) {
		$faultCode = 101; // disguise inexistent user
		$userlib->remove_user($user);
	    }

	    $user_msg = tra('XMLRPC Error: ') . $faultCode . ' - ' . tra($rpcauth->faultString());
	    $log_msg = tra('XMLRPC Error: ') . $rpcauth->faultCode() . ' - ' . tra($rpcauth->faultString());
	    $logslib->add_log('login','intertiki : '.$user.'@'.$_REQUEST['intertiki'].': '.$log_msg);
	    $smarty->assign('msg',$user_msg);
	    $smarty->display('error.tpl');
	    exit;
	} else {
	    $isvalid = true;
	    $isdue = false;

	    $logslib->add_log('login','intertiki : '.$user.'@'.$_REQUEST['intertiki']);

	    if (!empty($feature_intertiki_mymaster)) {

		// this is slave intertiki site
		$response_value = $rpcauth->value();
		$user_details = unserialize($response_value->scalarval());

		if (!$userlib->user_exists($user)) {
		    if ($userlib->add_user($user, '', $user_details['info']['email'])) {
		    	$userlib->set_user_fields($user_details['info']);
				} else {
					$logslib->add_log('login','intertiki : login creation failed');
					$smarty->assign('msg',tra('Unable to create login'));
					$smarty->display('error.tpl');
					die;
				}
		} else {
		    $userlib->set_user_fields($user_details['info']);
		    $userlib->update_lastlogin($user);
		}

		if ($feature_userPreferences == 'y' && $feature_intertiki_import_preferences == 'y') {
		    $userlib->set_user_preferences($user, $user_details['preferences']);
		}

		if ($feature_intertiki_import_groups == 'y') {
				if ($feature_intertiki_imported_groups) {
					$groups = preg_split('/\s*,\s*/',$feature_intertiki_imported_groups);
					foreach ($groups as $group) {
						if (in_array(trim($group),$user_details['groups'])) {
							$userlib->assign_user_to_group($user, trim($group));
						}
					}
				} else {
		    	$userlib->assign_user_to_groups($user, $user_details['groups']);
				}
		} else {
		    $groups = preg_split('/\s*,\s*/',$interlist[$feature_intertiki_mymaster]['groups']);
		    foreach ($groups as $group) {
			$userlib->assign_user_to_group($user, trim($group));
		    }
		}

	    } else {
		$user = $user.'@'.$_REQUEST['intertiki'];
		$feature_userPreferences = 'n';
		$smarty->assign('feature_userPreferences',$feature_userPreferences);
	    }

	}
    }
} else {

// Verify user is valid
list($isvalid, $user, $error) = $userlib->validate_user($user, $pass, $challenge, $response);

// If the password is valid but it is due then force the user to change the password by
// sending the user to the new password change screen without letting him use tiki
// The user must re-nter the old password so no security risk here
if ($isvalid) {
	$isdue = $userlib->is_due($user);
}
//}
}

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
		if (($url == $tikiIndex || basename($url) == $tikiIndex || urldecode(basename($url)) == $tikiIndex || $limitedGoGroupHome == "n") && $useGroupHome == 'y') { /* go to the group page only if the loginfrom is the default page */
			$groupHome = $tikilib->get_user_preference($user, 'homePage', $groupHome);
			$groupHome = $userlib->get_user_default_homepage($user);
    			if ($groupHome) {
                    $url = preg_match('#^https?:#', $groupHome) ? $groupHome : "tiki-index.php?page=".$groupHome;
    			}
		}
		//unset session variable in case user su's
		unset($_SESSION['loginfrom']);

		// No sense in sending user to registration page
		// This happens if the user has just registered and it's first login
		if (preg_match("/tiki-register.php/",$url)) {
		    $url = preg_replace("/tiki-register.php*$/","tiki-index.php",$url);
		}
		if (preg_match("/tiki-login_validate.php/",$url)) {
		    $url = preg_replace("/tiki-login_validate.php*$/","tiki-index.php",$url);
		}

		// Now if the remember me feature is on and the user checked the rememberme checkbox then ...
		if ($rememberme != 'disabled') {
			if (isset($_REQUEST['rme']) && $_REQUEST['rme'] == 'on') {
				$hash = $userlib->create_user_cookie($_REQUEST['user']);
				$time = substr($hash,strpos($hash,'.')+1);
				setcookie($user_cookie_site, $hash, $time, $cookie_path, $cookie_domain);
				$logslib->add_log('login',"got a cookie for $remembertime seconds");
			}
		}
	}
} else {
	unset($user);
	unset($isvalid);
	if ($error == PASSWORD_INCORRECT)
		$error = tra("Invalid password");
	else if ($error == USER_NOT_FOUND)
		$error = tra("Invalid username");
	else if ($error == ACCOUNT_DISABLED)
		$error = tra("Account disabled");
	else if ($error == USER_AMBIGOUS)
		$error = tra("You must use the right case for your user name");
	else
		$error= tra('Invalid username or password');
	$url = 'tiki-error.php?error=' . urlencode($error);
	// on a login error wait this long in seconds. slows down automated login attacks.
    // regular users mistyping on login will experience the delay, too, but wrong logins
    // shouldn't occur that often.
	sleep(5);
}

if ($https_mode) {
	$stay_in_ssl_mode = isset($_REQUEST['stay_in_ssl_mode']) && $_REQUEST['stay_in_ssl_mode'] == 'on';

	if (!$stay_in_ssl_mode) {
		$prefix      = 'http://';
		$http_domain = $tikilib->get_preference('http_domain', $_SERVER['SERVER_NAME']);
		$http_port   = $tikilib->get_preference('http_port', 80);

		if ($http_port != 80)
			$http_domain .= ':' . $http_port;

		$prefix .= $http_domain . '/';
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
