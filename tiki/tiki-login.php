<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-login.php,v 1.85.2.9 2008-03-19 13:17:26 jyhem Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$bypass_siteclose_check = 'y';
require_once('tiki-setup.php');

if ( ! (isset($_REQUEST['user']) or isset($_REQUEST['username'])) ) {
	header('Location: '.$base_url.'tiki-login_scr.php');
	die;
}
// Alert user if cookies are switched off
if ( ini_get('session.use_cookies') == 1 && ! isset($_COOKIE['PHPSESSID']) ) {
	header('Location: '.$base_url.'tiki-error.php?error='.urlencode(tra('You have to enable cookies to be able to login to this site')));
	die;
}

// Redirect to HTTPS if we are not in HTTPS but we require HTTPS login
if ( ! $https_mode && $prefs['https_login'] == 'required' ) {
	header('location: '.$base_url_https.$prefs['login_url']);
	exit;
}
// Redirect to HTTP if we are in HTTPS but we doesn't allow HTTPS login
if ( $https_mode && $prefs['https_login'] == 'disabled' ) {
	header('location: '.$base_url_http.$prefs['login_url']);
	exit;
}

// Remember where user is logging in from and send them back later; using session variable for those of us who use WebISO services
// Note that loginfrom will always be a complete URL (http://...)
if ( ! isset($_SESSION['loginfrom']) ) {
	$_SESSION['loginfrom'] = ( isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $prefs['tikiIndex'] );
	if ( ! ereg('^http', $_SESSION['loginfrom']) ) {
		if ( $_SESSION['loginfrom']{0} == '/' ) $_SESSION['loginfrom'] = $url_scheme.'://'.$url_host.(($url_port!='')?":$url_port":'').$_SESSION['loginfrom'];
		else $_SESSION['loginfrom'] = $base_url.$_SESSION['loginfrom'];
	}
}

if ( $tiki_p_admin == 'y' ) {
	if ( isset($_REQUEST['su']) ) {
		if ( $userlib->user_exists($_REQUEST['username']) ) {
			$_SESSION[$user_cookie_site] = $_REQUEST['username'];
			$smarty->assign_by_ref('user', $_REQUEST['username']);
		}

		header('location: '.$_SESSION['loginfrom']);
		// Unset session variable for the next su
		unset($_SESSION['loginfrom']);
		exit;
	}
}

$user = isset($_REQUEST['user']) ? $_REQUEST['user'] : false;
$pass = isset($_REQUEST['pass']) ? $_REQUEST['pass'] : false;
$challenge = isset($_REQUEST['challenge']) ? $_REQUEST['challenge'] : false;
$response = isset($_REQUEST['response']) ? $_REQUEST['response'] : false;
$isvalid = false;
$isdue = false;
$isEmailDue = false;

// admin is always local
if ( $user == 'admin' ) $prefs['feature_intertiki'] = 'n';

// Determine the intertiki domain
if ( $prefs['feature_intertiki'] == 'y' ) {
	if ( ! empty($prefs['feature_intertiki_mymaster']) ) $_REQUEST['intertiki'] = $prefs['feature_intertiki_mymaster'];
	elseif ( strstr($user, '@') ) {
		list($user, $intertiki_domain) = explode('@', $user);
		$_REQUEST['intertiki'] = $intertiki_domain;
	}
} else unset($_REQUEST['intertiki']);

// Go through the intertiki process
if ( isset($_REQUEST['intertiki']) and in_array($_REQUEST['intertiki'], array_keys($prefs['interlist'])) ) {

    $rpcauth = $userlib->intervalidate($prefs['interlist'][$_REQUEST['intertiki']],$user,$pass,!empty($prefs['feature_intertiki_mymaster'])? true : false);

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
		$isEmailDue = false;

	    $logslib->add_log('login','intertiki : '.$user.'@'.$_REQUEST['intertiki']);

	    if (!empty($prefs['feature_intertiki_mymaster'])) {

		// this is slave intertiki site
		$response_value = $rpcauth->value();
		$avatarData = '';
		if ($response_value->kindOf() == 'struct') {
			for (;;) {
				list($key, $value) = $response_value->structeach();
				if ($key == '') {
					break;
				} elseif ($key == 'user_details') {
					$user_details = unserialize($value->scalarval());
				} elseif ($key == 'avatarData') {
					$avatarData = $value->scalarval();
				}
			}
		} else {
			$user_details = unserialize($response_value->scalarval());
		}

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

		if ($prefs['feature_userPreferences'] == 'y' && $prefs['feature_intertiki_import_preferences'] == 'y') {
			global $userprefslib; include_once('lib/userprefs/userprefslib.php');
			$userprefslib->set_user_avatar($user, 'u', '', $user_details['avatarName'], $user_details['avatarSize'], $user_details['avatarFileType'], $avatarData);
		    $userlib->set_user_preferences($user, $user_details['preferences']);
		}

		if ($prefs['feature_intertiki_import_groups'] == 'y') {
				if ($prefs['feature_intertiki_imported_groups']) {
					$groups = preg_split('/\s*,\s*/',$prefs['feature_intertiki_imported_groups']);
					foreach ($groups as $group) {
						if (in_array(trim($group),$user_details['groups'])) {
							$userlib->assign_user_to_group($user, trim($group));
						}
					}
				} else {
		    	$userlib->assign_user_to_groups($user, $user_details['groups']);
				}
		} else {
		    $groups = preg_split('/\s*,\s*/',$prefs['interlist'][$prefs['feature_intertiki_mymaster']]['groups']);
		    foreach ($groups as $group) {
			$userlib->assign_user_to_group($user, trim($group));
		    }
		}

	    } else {
		$user = $user.'@'.$_REQUEST['intertiki'];
		$prefs['feature_userPreferences'] = 'n';
	    }

	}
    }
} else {

	// Verify user is valid
	list($isvalid, $user, $error) = $userlib->validate_user($user, $pass, $challenge, $response);

	// If the password is valid but it is due then force the user to change the password by
	// sending the user to the new password change screen without letting him use tiki
	// The user must re-nter the old password so no security risk here
	if ( $isvalid ) {
		$isdue = $userlib->is_due($user);
		if ( $user != 'admin' ) { // admin has not necessarely an email
			$isEmailDue = $userlib->is_email_due($user, 'email');

			// Update some user details from LDAP
			if ( is_array($user_ldap_attributes) ) {
				if ( $user_ldap_attributes['auth_ldap_nameattr'] != '' ) { 
					global $cachelib, $tikidomain;
					require_once('lib/cache/cachelib.php');
					$tikilib->set_user_preference($user, 'realName', $user_ldap_attributes['auth_ldap_nameattr']);
					// Erase cache to update displayed user info
					//   Do not just invalidate cache for 'user_details_'.$user and 'userslist',
					//   since userlink smarty modifier is also using cache with multiple possibilities of keys.
					$cachelib->erase_dir_content("temp/cache/$tikidomain");
				}
			}
		}
	}
}

if ( $isvalid ) {
	if ( $isdue ) {
		// Redirect the user to the screen where he must change his password.
		// Note that the user is not logged in he's just validated to change his password
		// The user must re-enter his old password so no security risk involved
		$url = 'tiki-change_password.php?user=' . urlencode($user);
	} elseif ($isEmailDue) {
		$userlib->send_confirm_email($user);
		$msg = $smarty->fetch('tiki-login_confirm_email.tpl');
		$smarty->assign_by_ref('msg', $msg);
		$smarty->assign('user', '');
		unset($user);
		$smarty->assign('do_not_show_login_box', 'y');
		$smarty->assign('mid', 'tiki-information.tpl');
		$smarty->display("tiki.tpl");
		die;
	} else {
		// User is valid and not due to change pass.. start session
		$_SESSION[$user_cookie_site] = $user;
		if( isset( $_SESSION['openid_url'] ) )
			$userlib->assign_openid( $user, $_SESSION['openid_url'] );

		$smarty->assign_by_ref('user', $user);
		$url = $_SESSION['loginfrom'];
		$logslib->add_log('login','logged from '.$url);

		// Special '?page=...' case. Accept only some values to avoid security problems
		switch ( $_REQUEST['page'] ) {
		case 'tikiIndex':
			$url = ${$_REQUEST['page']};
			break;
		default:
			// Go to the group page ?
			if ( $prefs['useGroupHome'] == 'y' ) {
				if ( $prefs['limitedGoGroupHome'] == 'y' ) {
					// Handle spaces (that could be written as '%20' in referer, but are generated as '+' with urlencode)
					$url = str_replace('%20', '+', $url);

					$url_vars = parse_url($url);
					$url_path = $url_vars['path'];
					if ( $url_vars['query'] != '' ) $url_path .= '?'.$url_vars['query'];

					// Get a valid URL for anonymous group homepage
					// It has to be rewritten when the following two syntaxes are used :
					//  - http:tiki-something.php => tiki-something.php
					//  - pageName => tiki-index.php?page=pageName
					$anonymous_homepage = $userlib->get_group_home('Anonymous');
					if ( ! ereg('^https?://', $anonymous_homepage) ) {
						if ( substr($anonymous_homepage, 0, 5) == 'http:' ) {
							$anonymous_homepage = substr($anonymous_homepage, 5);
						} else {
							$anonymous_homepage = 'tiki-index.php?page='.urlencode($anonymous_homepage);
						}
					}

					// Determine the complete tikiIndex URL for not logged users
					// when tikiIndex's page has not been explicitely specified
					//   (this only handles wiki default page for the moment)
					if ( ereg('tiki-index.php$', $prefs['site_tikiIndex'])
						|| ereg('tiki-index.php$', $anonymous_homepage)
					) {
						$tikiIndex_full = 'tiki-index.php?page='.urlencode($prefs['site_wikiHomePage']);
					} else {
						$tikiIndex_full = '';
					}
				}

				// Go to the group page instead of the referer url if we are in one of those cases :
				//   - pref 'Go to group homepage only if login from default homepage' (limitedGoGroupHome) is disabled,
				//   - referer url (e.g. http://example.com/tiki/tiki-index.php?page=Homepage ) is the homepage (tikiIndex),
				//   - referer url complete path ( e.g. /tiki/tiki-index.php?page=Homepage ) is the homepage,
				//   - referer url relative path ( e.g. tiki-index.php?page=Homepage ) is the homepage
				//   - one of the three cases listed above, but compared to anonymous page instead of global homepage
				//
				//   - last case ($tikiIndex_full != '') :
				//       wiki homepage could have been saved as 'tiki-index.php' instead of 'tiki-index.php?page=Homepage'.
				//       ... so we also need to check against : homepage + '?page=' + default wiki pagename
				//
				if ( $prefs['limitedGoGroupHome'] == 'n' 
					|| $url == $prefs['site_tikiIndex']
					|| $url_path == $prefs['site_tikiIndex']
					|| basename($url_path) == $prefs['site_tikiIndex']
					|| ( $anonymous_homepage != '' &&
						( $url == $anonymous_homepage
						|| $url_path == $anonymous_homepage
						|| basename($url_path) == $anonymous_homepage
						)
					)
					|| ( $tikiIndex_full != '' && basename($url_path) == $tikiIndex_full )
				) {
					$groupHome = $userlib->get_user_default_homepage($user);
					if ( $groupHome != '' ) $url = ( preg_match('/^(\/|https?:)/', $groupHome) ) ? $groupHome : 'tiki-index.php?page='.urlencode($groupHome);
				}
			}
	
			// Unset session variable in case user su's
			unset($_SESSION['loginfrom']);
	
			// No sense in sending user to registration page or no page at all
			// This happens if the user has just registered and it's first login
			if ( $url == '' || ereg('(tiki-register|tiki-login_validate|tiki-login_scr)\.php', $url) ) $url = $prefs['tikiIndex'];
	
			// Now if the remember me feature is on and the user checked the rememberme checkbox then ...
			if ( $prefs['rememberme'] != 'disabled' ) {
				if ( isset($_REQUEST['rme']) && $_REQUEST['rme'] == 'on' ) {
					$hash = $userlib->create_user_cookie($_REQUEST['user']);
					$time = substr($hash,strpos($hash,'.')+1);
					setcookie($user_cookie_site, $hash.'.'.$user, $time, $cookie_path, $prefs['cookie_domain']);
					$logslib->add_log('login','got a cookie for '.$prefs['remembertime'].' seconds');
				}
			}
		}
	}
} else {
	if ( $error == PASSWORD_INCORRECT && $prefs['unsuccessful_logins'] >= 0 ) {
 		if ( ($nb_bad_logins = $userlib->unsuccessful_logins($user)) >= $prefs['unsuccessful_logins'] ) {
			$msg = sprintf(tra('More than %d unsuccessful login attempts have been made.'), $prefs['unsuccessful_logins']);
			$smarty->assign('msg', $msg);
			$userlib->send_confirm_email($user, 'unsuccessful_logins');
			$smarty->assign('msg', $msg.' '.tra('An email has been sent to you with the instructions to follow.'));
			$smarty->assign('user', '');
			unset($user);
			$smarty->assign('mid', 'tiki-information.tpl');
			$smarty->display("tiki.tpl");
			die;
		}
		$userlib->set_unsuccessful_logins($user, $nb_bad_logins + 1);
	}
	unset($user);
	unset($isvalid);

	switch ( $error ) {
	case PASSWORD_INCORRECT: $error = tra('Invalid password'); break;
	case USER_NOT_FOUND: $error = tra('Invalid username'); break;
	case ACCOUNT_DISABLED: $error = tra('Account disabled'); break;
	case USER_AMBIGOUS: $error = tra('You must use the right case for your user name'); break;
	case USER_NOT_VALIDATED: $error = tra('You are not yet validated'); break;
	default: $error = tra('Invalid username or password');
	}
	$url = 'tiki-error.php?error='.urlencode($error);

	// on a login error wait this long in seconds. slows down automated login attacks.
	// regular users mistyping on login will experience the delay, too, but wrong logins
	// shouldn't occur that often.
	sleep(5);
}

if ( isset($user) and $prefs['feature_score'] == 'y' ) $tikilib->score_event($user, 'login');


// RFC 2616 defines that the 'Location' HTTP headerconsists of an absolute URI
if ( ! eregi('^https?\:', $url) ) $url = ( ereg('^/', $url) ? $url_scheme.'://'.$url_host.(($url_port!='')?":$url_port":'') : $base_url ).$url;

// Force HTTP mode if needed
if ( $stay_in_ssl_mode != 'y' || ! $https_mode ) $url = str_replace('https://', 'http://', $url);

if ( SID ) $url .= (( strpos('?', $url) === false ) ? '?' : '').SID;
header('Location: '.$url);
exit;
?>
