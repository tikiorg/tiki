<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$bypass_siteclose_check = 'y';
require_once ('tiki-setup.php');
$login_url_params = '';

if (isset($_REQUEST['cas']) && $_REQUEST['cas'] == 'y' && $prefs['auth_method'] == 'cas') {
	$login_url_params = '?cas=y';
	$_REQUEST['user'] = '';
} elseif (!(isset($_REQUEST['user']) or isset($_REQUEST['username']))) {
	if (!$https_mode && $prefs['https_login'] == 'required') {
		header('Location: ' . $base_url_https . 'tiki-login_scr.php');
	} else {
		header('Location: ' . $base_url . 'tiki-login_scr.php');
	}
	die;
}
$smarty->assign('errortype', 'login'); // to avoid any redirection to the login box if error
// Alert user if cookies are switched off
if (ini_get('session.use_cookies') == 1 && !isset($_COOKIE[ session_name() ]) && $prefs['session_silent'] != 'y') {
	$smarty->assign('msg', tra('You have to enable cookies to be able to login to this site'));
	$smarty->display('error.tpl');
	exit;
}

// Redirect to HTTPS if we are not in HTTPS but we require HTTPS login
if (!$https_mode && $prefs['https_login'] == 'required') {
	header( 'Location: ' . $base_url_https . $prefs['login_url'] . $login_url_params );
	exit;
}
// Redirect to HTTP if we are in HTTPS but we doesn't allow HTTPS login
if ($https_mode && $prefs['https_login'] == 'disabled') {
	header( 'Location: ' . $base_url_http . $prefs['login_url'] . $login_url_params );
	exit;
}

if( $prefs['session_silent'] == 'y' ) {
	session_start();
}

// Remember where user is logging in from and send them back later; using session variable for those of us who use WebISO services
// Note that login from will always be a complete URL (http://...)
if (!isset($_SESSION['loginfrom'])) {
	$_SESSION['loginfrom'] = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $prefs['tikiIndex']);
	if (!preg_match('/^http/', $_SESSION['loginfrom'])) {
		if ($_SESSION['loginfrom'] {
			0
		} == '/') $_SESSION['loginfrom'] = $url_scheme . '://' . $url_host . (($url_port != '') ? ":$url_port" : '') . $_SESSION['loginfrom'];
		else $_SESSION['loginfrom'] = $base_url . $_SESSION['loginfrom'];
	}
}
if ($tiki_p_admin == 'y') {
	if (isset($_REQUEST['su'])) {
		if( empty( $_REQUEST['username'] ) ) {
			$smarty->assign('msg', tra('Username field cannot be empty. Please go back and try again.'));
			$smarty->display('error.tpl');
			exit;
		}
		if ($userlib->user_exists($_REQUEST['username'])) {
			$_SESSION[$user_cookie_site] = $_REQUEST['username'];
			$smarty->assign_by_ref('user', $_REQUEST['username']);
		}
		header('location: ' . $_SESSION['loginfrom']);
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
if ($user == 'admin') $prefs['feature_intertiki'] = 'n';
// Determine the intertiki domain
if ($prefs['feature_intertiki'] == 'y') {
	if (!empty($prefs['feature_intertiki_mymaster'])) $_REQUEST['intertiki'] = $prefs['feature_intertiki_mymaster'];
	elseif (strstr($user, '@')) {
		list($user, $intertiki_domain) = explode('@', $user);
		$_REQUEST['intertiki'] = $intertiki_domain;
	}
} else unset($_REQUEST['intertiki']);
// Go through the intertiki process
if (isset($_REQUEST['intertiki']) and in_array($_REQUEST['intertiki'], array_keys($prefs['interlist']))) {
	$rpcauth = $userlib->intervalidate($prefs['interlist'][$_REQUEST['intertiki']], $user, $pass, !empty($prefs['feature_intertiki_mymaster']) ? true : false);
	if (!$rpcauth) {
		$logslib->add_log('login', 'intertiki : ' . $user . '@' . $_REQUEST['intertiki'] . ': Failed');
		$smarty->assign('msg', tra('Unable to contact remote server.'));
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
			$logslib->add_log('login', 'intertiki : ' . $user . '@' . $_REQUEST['intertiki'] . ': ' . $log_msg);
			$smarty->assign('msg', $user_msg);
			$smarty->display('error.tpl');
			exit;
		} else {
			$isvalid = true;
			$isdue = false;
			$isEmailDue = false;
			$logslib->add_log('login', 'intertiki : ' . $user . '@' . $_REQUEST['intertiki']);
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
				$user = $user_details['info']['login']; // use the correct caps
				if (!$userlib->user_exists($user)) {
					if ($userlib->add_user($user, '', $user_details['info']['email'])) {
						$userlib->set_user_fields($user_details['info']);
					} else {
						$logslib->add_log('login', 'intertiki : login creation failed');
						$smarty->assign('msg', tra('Unable to create login'));
						$smarty->display('error.tpl');
						die;
					}
				} else {
					$userlib->set_user_fields($user_details['info']);
					$userlib->update_lastlogin($user);
				}
				if ($prefs['feature_userPreferences'] == 'y' && $prefs['feature_intertiki_import_preferences'] == 'y') {
					global $userprefslib;
					include_once ('lib/userprefs/userprefslib.php');
					$userprefslib->set_user_avatar($user, 'u', '', $user_details['avatarName'], $user_details['avatarSize'], $user_details['avatarFileType'], $avatarData);
					$userlib->set_user_preferences($user, $user_details['preferences']);
				}
				if ($prefs['feature_intertiki_import_groups'] == 'y') {
					if ($prefs['feature_intertiki_imported_groups']) {
						$groups = preg_split('/\s*,\s*/', $prefs['feature_intertiki_imported_groups']);
						foreach($groups as $group) {
							if (in_array(trim($group), $user_details['groups'])) {
								$userlib->assign_user_to_group($user, trim($group));
							}
						}
					} else {
						$userlib->assign_user_to_groups($user, $user_details['groups']);
					}
				} else {
					$groups = preg_split('/\s*,\s*/', $prefs['interlist'][$prefs['feature_intertiki_mymaster']]['groups']);
					foreach($groups as $group) {
						$userlib->assign_user_to_group($user, trim($group));
					}
				}
			} else {
				$user = $user . '@' . $_REQUEST['intertiki'];
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
	if ($isvalid) {
		$isdue = $userlib->is_due($user);
		if ($user != 'admin') { // admin has not necessarely an email
			$isEmailDue = $userlib->is_email_due($user, 'email');
			// Update some user details from LDAP
			if (is_array($user_ldap_attributes)) {
				if (count($user_ldap_attributes) > 0) {
					global $cachelib, $tikidomain;
					require_once ('lib/cache/cachelib.php');
					if ($user_ldap_attributes['auth_ldap_nameattr'] != '') {
						$tikilib->set_user_preference($user, 'realName', $user_ldap_attributes['auth_ldap_nameattr']);
					}
					if ($user_ldap_attributes['auth_ldap_countryattr'] != '') {
						$tikilib->set_user_preference($user, 'country', $user_ldap_attributes['auth_ldap_countryattr']);
					}
					if ($user_ldap_attributes['auth_ldap_emailattr'] != '') {
						$userlib->change_user_email($user, $user_ldap_attributes['auth_ldap_emailattr'], '');
					}
					// Erase cache to update displayed user info
					//   Do not just invalidate cache for 'user_details_'.$user and 'userslist',
					//   since userlink smarty modifier is also using cache with multiple possibilities of keys.
					$cachelib->empty_cache('temp_cache', 'login');
				}
			}
		}
	}
}
if ($isvalid) {

    if ($prefs['feature_invite'] == 'y') {
        // tiki-invite, this part is just here to add groups to users which just registered after received an
        // invitation via tiki-invite.php and set the redirect to wiki page if required by the invitation
        $res = $tikilib->query("SELECT `id`,`id_invite` FROM `tiki_invited` WHERE `used_on_user`=? AND used=?", array($user, "registered"));
        $inviterow=$res->fetchRow();
        if (is_array($inviterow)) {
            $id_invited=$inviterow['id'];
            $id_invite=$inviterow['id_invite'];
            // set groups
            
            $groups = $tikilib->getOne("SELECT `groups` FROM `tiki_invite` WHERE `id` = ?", array((int)$id_invite));
            $groups = explode(',', $groups);
            foreach ($groups as $group)
                $userlib->assign_user_to_group($user, trim($group));
            $tikilib->query("UPDATE `tiki_invited` SET `used`=? WHERE id_invite=?", array("logged", (int)$id_invited));
            
            // set wiki page required by invitation
            if (!empty($inviterow['wikipageafter'])) $_REQUEST['page']=$inviterow['wikipageafter'];
        }
    }

	if ($isdue) {
		// Redirect the user to the screen where he must change his password.
		// Note that the user is not logged in he's just validated to change his password
		// The user must re-enter his old password so no security risk involved
		$url = 'tiki-change_password.php?user=' . urlencode($user);
	} elseif ($isEmailDue) {
		$userlib->send_confirm_email($user);
		$userlib->change_user_waiting($user, 'u');
		$msg = $smarty->fetch('tiki-login_confirm_email.tpl');
		$smarty->assign_by_ref('msg', explode("\n", $msg));
		$smarty->assign('user', '');
		unset($user);
		$smarty->assign('mid', 'tiki-information.tpl');
		$smarty->display("tiki.tpl");
		die;
	} else {
		// User is valid and not due to change pass.. start session
		$userlib->update_expired_groups();
		$_SESSION[$user_cookie_site] = $user;
		if (isset($_SESSION['openid_url'])) $userlib->assign_openid($user, $_SESSION['openid_url']);
		$smarty->assign_by_ref('user', $user);
		$url = $_SESSION['loginfrom'];
		$logslib->add_log('login', 'logged from ' . $url);
		// Special '?page=...' case. Accept only some values to avoid security problems
		if ( isset($_REQUEST['page']) and $_REQUEST['page'] === 'tikiIndex') {
				$url = ${$_REQUEST['page']};
		} else {	
				if (!empty($_REQUEST['url'])) {
					global $cachelib; include_once('lib/cache/cachelib.php');
					preg_match('/(.*)\?cache=(.*)/', $_REQUEST['url'], $matches);
					if (!empty($matches[2]) && $cdata = $cachelib->getCached($matches[2], 'edit')) {
						if (!empty($matches[1])) {
							$url = $matches[1].'?'.$cdata;
						}
						$cachelib->invalidate($matches[2], 'edit');
					}
				} elseif ($prefs['useGroupHome'] == 'y') { // Go to the group page ?
					if ($prefs['limitedGoGroupHome'] == 'y') {
						// Handle spaces (that could be written as '%20' in referer, but are generated as '+' with urlencode)
						$url = str_replace('%20', '+', $url);
						$url_vars = parse_url($url);
						$url_path = $url_vars['path'];
						if ($url_vars['query'] != '') $url_path.= '?' . $url_vars['query'];
						// Get a valid URL for anonymous group homepage
						// It has to be rewritten when the following two syntaxes are used :
						//  - http:tiki-something.php => tiki-something.php
						//  - pageName => tiki-index.php?page=pageName
						$anonymous_homepage = $userlib->get_group_home('Anonymous');
						if (!preg_match('#^https?://#', $anonymous_homepage)) {
							if (substr($anonymous_homepage, 0, 5) == 'http:') {
								$anonymous_homepage = substr($anonymous_homepage, 5);
							} else {
								$anonymous_homepage = 'tiki-index.php?page=' . urlencode($anonymous_homepage);
							}
						}
						// Determine the complete tikiIndex URL for not logged users
						// when tikiIndex's page has not been explicitely specified
						//   (this only handles wiki default page for the moment)
						if (preg_match('/tiki-index.php$/', $prefs['site_tikiIndex']) || preg_match('/tiki-index.php$/', $anonymous_homepage)) {
							$tikiIndex_full = 'tiki-index.php?page=' . urlencode($prefs['site_wikiHomePage']);
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
					if ($prefs['limitedGoGroupHome'] == 'n' || $url == $prefs['site_tikiIndex'] || $url_path == $prefs['site_tikiIndex'] || basename($url_path) == $prefs['site_tikiIndex'] || ($anonymous_homepage != '' && ($url == $anonymous_homepage || $url_path == $anonymous_homepage || basename($url_path) == $anonymous_homepage)) || ($tikiIndex_full != '' && basename($url_path) == $tikiIndex_full)) {
						$groupHome = $userlib->get_user_default_homepage($user);
						if ($groupHome != '') $url = (preg_match('/^(\/|https?:)/', $groupHome)) ? $groupHome : 'tiki-index.php?page=' . urlencode($groupHome);
					}
				}
				// Unset session variable in case user su's
				unset($_SESSION['loginfrom']);
				// No sense in sending user to registration page or no page at all
				// This happens if the user has just registered and it's first login
				if ($url == '' || preg_match('/(tiki-register|tiki-login_validate|tiki-login_scr)\.php/', $url)) $url = $prefs['tikiIndex'];
				// Now if the remember me feature is on and the user checked the rememberme checkbox then ...
				if ($prefs['rememberme'] != 'disabled' && isset($_REQUEST['rme']) && $_REQUEST['rme'] == 'on') {
						$userInfo = $userlib->get_user_info($user);
						$userId = $userInfo['userId'];
						$secret = $userlib->create_user_cookie($userId);
						setcookie($user_cookie_site, $secret . '.' . $userId, $tikilib->now + $prefs['remembertime'], $prefs['cookie_path'], $prefs['cookie_domain']);
						$logslib->add_log('login', 'got a cookie for ' . $prefs['remembertime'] . ' seconds');
				}
			}
		}
	} else {	// if ($isvalid)
		// not valid - check if site is closed first
		if ($prefs['site_closed'] === 'y') {
			unset($bypass_siteclose_check);
			include 'lib/setup/site_closed.php';
		}
		
		if (isset($_REQUEST['url'])) {
			$smarty->assign('url', $_REQUEST['url']);
		}
		if ($error == PASSWORD_INCORRECT && ($prefs['unsuccessful_logins'] >= 0 || $prefs['unsuccessful_logins_invalid'] >= 0)) {
			$nb_bad_logins = $userlib->unsuccessful_logins($user);
			if ($prefs['unsuccessful_logins_invalid'] > 0 && ($nb_bad_logins >= $prefs['unsuccessful_logins_invalid'] - 1)) {
				$info = $userlib->get_user_info($user);
				$userlib->change_user_waiting($user, 'a');
				$msg = sprintf(tra('More than %d unsuccessful login attempts have been made.'), $prefs['unsuccessful_logins_invalid']);
				$msg .= ' '.tra('Your account has been suspended.').' '.tra('A site administrator will reactivate it');
				include_once ('lib/webmail/tikimaillib.php');
				$mail = new TikiMail();
				$smarty->assign('msg', $msg);
				$smarty->assign('mail_user', $user);
				$foo = parse_url($_SERVER['REQUEST_URI']);
				$mail_machine = $tikilib->httpPrefix( true ).str_replace('tiki-login.php', '', $foo['path']);
				$smarty->assign('mail_machine', $mail_machine);
				$mail->setText($smarty->fetch('mail/unsuccessful_logins_suspend.tpl'));
				$mail->setSubject($smarty->fetch('mail/unsuccessful_logins_suspend_subject.tpl'));
				$emails = !empty($prefs['validator_emails'])?preg_split('/,/', $prefs['validator_emails']): (!empty($prefs['sender_email'])? array($prefs['sender_email']): '');
				if (!$mail->send(array($info['email'])) || !$mail->send($emails)) {
					$smarty->assign('msg', tra("The mail can't be sent. Contact the administrator"));
					$smarty->display("error.tpl");
					die;
				}
				$smarty->assign('user', '');
				unset($user);
				$smarty->assign('mid', 'tiki-information.tpl');
				$smarty->display('tiki.tpl');
				die;
			} elseif ($prefs['unsuccessful_logins'] > 0 && ($nb_bad_logins >= $prefs['unsuccessful_logins'] - 1)) {
				$msg = sprintf(tra('More than %d unsuccessful login attempts have been made.'), $prefs['unsuccessful_logins']);
				$smarty->assign('msg', $msg);
				if ($userlib->send_confirm_email($user, 'unsuccessful_logins')) {
					$smarty->assign('msg', $msg . ' ' . tra('An email has been sent to you with the instructions to follow.'));
				}
				$smarty->assign('user', '');
				unset($user);
				$show_history_back_link = 'y';
				$smarty->assign_by_ref('show_history_back_link', $show_history_back_link);
				$smarty->assign('mid', 'tiki-information.tpl');
				$smarty->display("tiki.tpl");
				die;
			}
			$userlib->set_unsuccessful_logins($user, $nb_bad_logins + 1);
		}
		unset($user); // Important so that modules are showing based on anonymous
		unset($isvalid);
		switch ($error) {
			case PASSWORD_INCORRECT:
			case USER_NOT_FOUND:
				$smarty->assign('error_login', $error);
				$smarty->assign('mid', 'tiki-login.tpl');
				$smarty->assign('error_user', $_REQUEST["user"]);
				$smarty->display('tiki.tpl');
				exit;

			case ACCOUNT_DISABLED:
				$error = 'Account disabled';
				break;

			case ACCOUNT_WAITING_USER:
				$error = 'You did not validate your account';
				$extraButton = array('href'=>'tiki-send_mail.php?user='.$_REQUEST['user'], 'text'=>tra('Resend'), 'comment'=>tra('You should have received an email. Check your mailbox and your spam box. Otherwise click on the button to resend the email')); 
				break;

			case USER_AMBIGOUS:
				$error = 'You must use the right case for your user name';
				break;

			case USER_NOT_VALIDATED:
				$error = 'You are not yet validated';
				break;

			default:
				$error = 'Invalid username or password';
		}
		if (isset($extraButton)) $smarty->assign_by_ref('extraButton', $extraButton);
		$smarty->assign('msg', tra($error));
		$smarty->display('error.tpl');
		exit;
		// on a login error wait this long in seconds. slows down automated login attacks.
		// regular users mistyping on login will experience the delay, too, but wrong logins
		// shouldn't occur that often.
		sleep(5);
	}

	if ( isset($user) and $prefs['feature_score'] == 'y' ) {
		$tikilib->score_event($user, 'login');
	}
	// RFC 2616 defines that the 'Location' HTTP headerconsists of an absolute URI
	if ( !preg_match('/^https?\:/i', $url) ) {
		$url = (preg_match('/^\//', $url) ? $url_scheme . '://' . $url_host . (($url_port != '') ? ":$url_port" : '') : $base_url) . $url;
	}
	// Force HTTP mode if needed
	if ($stay_in_ssl_mode != 'y' || !$https_mode) {
		$url = str_replace('https://', 'http://', $url);
	}
	// Force Redirection to HTTPS mode of original URL if needed
	if ($stay_in_ssl_mode == 'y' && $https_mode) {
		$url = str_replace('http://', 'https://', $url);
	}
	if (defined('SID') && SID != '')
		$url.= ((strpos($url, '?') === false) ? '?' : '&') . SID;
	if ( isset($_SESSION['cas_redirect']) ) {
		$url = $_SESSION['cas_redirect'];
		unset($_SESSION['cas_redirect']);
		$_SESSION[$user_cookie_site] = $user;
	} 

	header('Location: ' . $url);
	exit;
	
