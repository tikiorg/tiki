<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$inputConfiguration = array(
	array( 'staticKeyFilters' => array(
		'user' => 'text',
		'username' => 'text',
		'pass' => 'none',
	) )
);

$bypass_siteclose_check = 'y';

if (empty($_POST['user'])) {
	unset($_POST['user']);	// $_POST['user'] is not allowed to be empty if set in tiki-setup.php
}
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
	$smarty->assign('msg', tra('Cookies must be enabled to log in to this site'));
	$smarty->display('error.tpl');
	exit;
}

// Redirect to HTTPS if we are not in HTTPS but we require HTTPS login
if (!$https_mode && $prefs['https_login'] == 'required') {
	header('Location: ' . $base_url_https . $prefs['login_url'] . $login_url_params);
	exit;
}
// Redirect to HTTP if we are in HTTPS but we doesn't allow HTTPS login
if ($https_mode && $prefs['https_login'] == 'disabled') {
	header('Location: ' . $base_url_http . $prefs['login_url'] . $login_url_params);
	exit;
}

if ( $prefs['session_silent'] == 'y' ) {
	session_start();
}

// Remember where user is logging in from and send them back later; using session variable for those of us who use WebISO services
// Note that login from will always be a complete URL (http://...)
if (!isset($_SESSION['loginfrom']) && isset($_SERVER['HTTP_REFERER']) && !preg_match('|/login|', $_SERVER['HTTP_REFERER']) && !preg_match('|logout|', $_SERVER['HTTP_REFERER'])) {
	$_SESSION['loginfrom'] = $_SERVER['HTTP_REFERER'];
	if (!preg_match('/^http/', $_SESSION['loginfrom'])) {
		if ($_SESSION['loginfrom'] {
			0
		} == '/') $_SESSION['loginfrom'] = $url_scheme . '://' . $url_host . (($url_port != '') ? ":$url_port" : '') . $_SESSION['loginfrom'];
		else $_SESSION['loginfrom'] = $base_url . $_SESSION['loginfrom'];
	}
}
if (isset($_REQUEST['su'])) {
	$loginlib = TikiLib::lib('login');

	if ($loginlib->isSwitched() && $_REQUEST['su'] == 'revert') {
		$loginlib->revertSwitch();
		$access->redirect($_SESSION['loginfrom']);
	} elseif ($tiki_p_admin == 'y') {
		if ( empty( $_REQUEST['username'] ) ) {
			$smarty->assign('msg', tra('Username field cannot be empty. Please go back and try again.'));
			$smarty->display('error.tpl');
			exit;
		}
		if ($userlib->user_exists($_REQUEST['username'])) {
			$loginlib->switchUser($_REQUEST['username']);
		}
		
		$access->redirect($_SESSION['loginfrom']);
	}
}
$requestedUser = isset($_REQUEST['user']) ? $_REQUEST['user'] : false;
$pass = isset($_REQUEST['pass']) ? $_REQUEST['pass'] : false;
$challenge = isset($_REQUEST['challenge']) ? $_REQUEST['challenge'] : false;
$response = isset($_REQUEST['response']) ? $_REQUEST['response'] : false;
$isvalid = false;
$isdue = false;
// admin is always local
if ($requestedUser == 'admin') $prefs['feature_intertiki'] = 'n';
// Determine the intertiki domain
if ($prefs['feature_intertiki'] == 'y') {
	if (!empty($prefs['feature_intertiki_mymaster'])) {
		$_REQUEST['intertiki'] = $prefs['feature_intertiki_mymaster'];
	} elseif (strstr($requestedUser, '@')) {
		list($requestedUser, $intertiki_domain) = explode('@', $requestedUser);
		$_REQUEST['intertiki'] = $intertiki_domain;
	}
} else unset($_REQUEST['intertiki']);
// Go through the intertiki process
if (isset($_REQUEST['intertiki']) and in_array($_REQUEST['intertiki'], array_keys($prefs['interlist']))) {
	$rpcauth = $userlib->intervalidate($prefs['interlist'][$_REQUEST['intertiki']], $requestedUser, $pass, !empty($prefs['feature_intertiki_mymaster']) ? true : false);
	if (!$rpcauth) {
		$logslib->add_log('login', 'intertiki : ' . $requestedUser . '@' . $_REQUEST['intertiki'] . ': Failed');
		$smarty->assign('msg', tra('Unable to contact remote server.'));
		$smarty->display('error.tpl');
		exit;
	} else {
		if ($faultCode = $rpcauth->faultCode()) {
			if ($faultCode == 102) {
				$faultCode = 101; // disguise inexistent user
				$userlib->remove_user($requestedUser);
			}
			$user_msg = tra('XMLRPC Error: ') . $faultCode . ' - ' . tra($rpcauth->faultString());
			$log_msg = tra('XMLRPC Error: ') . $rpcauth->faultCode() . ' - ' . tra($rpcauth->faultString());
			$logslib->add_log('login', 'intertiki : ' . $requestedUser . '@' . $_REQUEST['intertiki'] . ': ' . $log_msg);
			$smarty->assign('msg', $user_msg);
			$smarty->display('error.tpl');
			exit;
		} else {
			$isvalid = true;
			$isdue = false;
			$logslib->add_log('login', 'intertiki : ' . $requestedUser . '@' . $_REQUEST['intertiki']);
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
				$requestedUser = $user_details['info']['login']; // use the correct capitalization
				if (!$userlib->user_exists($requestedUser)) {
					if (!$userlib->add_user($requestedUser, '', $user_details['info']['email'])) {
						$logslib->add_log('login', 'intertiki : login creation failed');
						$smarty->assign('msg', tra('Unable to create login'));
						$smarty->display('error.tpl');
						die;
					}
				} else {
					$userlib->update_lastlogin($requestedUser);
				}
				$userlib->set_user_fields($user_details['info']);
				$user = $requestedUser;
				if ($prefs['feature_userPreferences'] == 'y' && $prefs['feature_intertiki_import_preferences'] == 'y') {
					$userprefslib = TikiLib::lib('userprefs');
					if (!empty($avatarData)) {
						$userprefslib->set_user_avatar($user, 'u', '', $user_details['info']['avatarName'], $user_details['info']['avatarSize'], $user_details['info']['avatarFileType'], $avatarData, false);
					}
					$userlib->set_user_preferences($user, $user_details['preferences']);
				}
				if ($prefs['feature_intertiki_import_groups'] == 'y') {
					if ($prefs['feature_intertiki_imported_groups']) {
						$groups = preg_split('/\s*,\s*/', $prefs['feature_intertiki_imported_groups']);
						foreach ($groups as $group) {
							if (in_array(trim($group), $user_details['groups']) && $userlib->group_exists(trim($group))) {
								$userlib->assign_user_to_group($user, trim($group));
							}
						}
					} elseif ($userlib->group_exists($user_details['groups'])) {
						$userlib->assign_user_to_groups($user, $user_details['groups']);
					}
				} else {
					$groups = preg_split('/\s*,\s*/', $prefs['interlist'][$prefs['feature_intertiki_mymaster']]['groups']);
					foreach ($groups as $group) {
						$userlib->assign_user_to_group($user, trim($group));
					}
				}
			} else {
				$user = $requestedUser . '@' . $_REQUEST['intertiki'];
				$prefs['feature_userPreferences'] = 'n';
			}
		}
	}
} else {
	// Verify user is valid
	$ret = $userlib->validate_user($requestedUser, $pass, $challenge, $response);
	if (count($ret) == 3) {
		$ret[] = null;
	}
	list($isvalid, $requestedUser, $error, $method) = $ret;
	// If the password is valid but it is due then force the user to change the password by
	// sending the user to the new password change screen without letting him use tiki
	// The user must re-enter the old password so no security risk here
	if (!$isvalid && $error === ACCOUNT_WAITING_USER) {
		if ($requestedUser != 'admin') { // admin has not necessarely an email

			if ($userlib->is_email_due($requestedUser, 'email')) {

				$userlib->send_confirm_email($requestedUser);
				$userlib->change_user_waiting($requestedUser, 'u');
				$user = '';
				$smarty->assign('user', '');
				$msg = $smarty->fetch('tiki-login_confirm_email.tpl');
				$smarty->assign_by_ref('msg', explode("\n", $msg));
				$smarty->assign('mid', 'tiki-information.tpl');
				$smarty->display("tiki.tpl");
				die;
			}
		}
	} else if ($isvalid) {
		$isdue = $userlib->is_due($requestedUser, $method);
		$user = $requestedUser;
	}
}
if ($isvalid) {
        $userlib->set_unsuccessful_logins($requestedUser, 0);
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
			foreach ($groups as $group) {
				$userlib->assign_user_to_group($user, trim($group));
			}
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
	} else {
		// User is valid and not due to change pass.. start session
		$userlib->update_expired_groups();
		TikiLib::lib('login')->activateSession($user);
		if (isset($_SESSION['openid_url'])) $userlib->assign_openid($user, $_SESSION['openid_url']);
		$url = $_SESSION['loginfrom'];

		// When logging into a multi-lingual Tiki, $_SESSION['loginfrom'] contains the main-language page, and not the translated one
		//	This only applies if feature_best_language and only seems to affect SEFURL
		if (($prefs['feature_best_language'] == 'y')&&($prefs['feature_sefurl'] == 'y')) {
			// If the URL contains the 'main' home page, remove the page name and let Tiki choose the correct home page upon reload
			$homePageUrl = urlencode($prefs['wikiHomePage']);
			if (strpos($url, 'page='. $homePageUrl) !== false) {
				$url = str_replace('page='. $homePageUrl, '', $url);
			} else if (strpos($url, $homePageUrl) !== false) {
				// Strip away the page name from the URL
				$parts = parse_url($url);
				$url = '';
				if (!empty($parts['scheme'])) {
					$url = $parts['scheme'].'://';
				}
				if (!empty($parts['host'])) {
					$url .= $parts['host'];
				}
				if (!empty($parts['path'])) {
					$pathParts = explode('/', $parts['path']);
					$cnt = count($pathParts);
					if ($cnt > 0) {
						$pathParts[$cnt-1] = null;	// Drop the page name
					}
					$newPath .= implode('/', $pathParts);
					$url .= $newPath;
				}
			}
		}

		$logslib->add_log('login', 'logged from ' . $url);
		// Special '?page=...' case. Accept only some values to avoid security problems
		if ( isset($_REQUEST['page']) and $_REQUEST['page'] === 'tikiIndex') {
			$url = ${$_REQUEST['page']};
		} else {
			if (!empty($_REQUEST['url'])) {
				$cachelib = TikiLib::lib('cache');
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
				//   - pref 'Go to the group homepage only if logging in from the default homepage' (limitedGoGroupHome) is disabled,
				//   - referer url (e.g. http://example.com/tiki/tiki-index.php?page=Homepage ) is the homepage (tikiIndex),
				//   - referer url complete path ( e.g. /tiki/tiki-index.php?page=Homepage ) is the homepage,
				//   - referer url relative path ( e.g. tiki-index.php?page=Homepage ) is the homepage
				//   - referer url SEF page ( e.g. /tiki/Homepage ) is the homepage
				//   - one of the three cases listed above, but compared to anonymous page instead of global homepage
				//   - first login after registration
				//   - last case ($tikiIndex_full != '') :
				//       wiki homepage could have been saved as 'tiki-index.php' instead of 'tiki-index.php?page=Homepage'.
				//       ... so we also need to check against : homepage + '?page=' + default wiki pagename
				//
				include_once('tiki-sefurl.php');
				if ($url == '' || preg_match('/(tiki-register|tiki-login_validate|tiki-login_scr)\.php/', $url) || $prefs['limitedGoGroupHome'] == 'n' || $url == $prefs['site_tikiIndex'] || $url_path == $prefs['site_tikiIndex'] || basename($url_path) == $prefs['site_tikiIndex'] || ($anonymous_homepage != '' && ($url == $anonymous_homepage || $url_path == $anonymous_homepage || basename($url_path) == $anonymous_homepage)) || filter_out_sefurl($anonymous_homepage) == basename($url_path) || ($tikiIndex_full != '' && basename($url_path) == $tikiIndex_full)) {
					$groupHome = $userlib->get_user_default_homepage($user);
					if ($groupHome != '') {
						$url = (preg_match('/^(\/|https?:)/', $groupHome)) ? $groupHome : filter_out_sefurl('tiki-index.php?page=' . urlencode($groupHome));
					}
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
} else {	// if ($isvalid) - invalid
	// check if site is closed
	if ($prefs['site_closed'] === 'y') {
		unset($bypass_siteclose_check);
		include 'lib/setup/site_closed.php';
	}

	if (isset($_REQUEST['url'])) {
		$smarty->assign('url', $_REQUEST['url']);
	}
	$module_params['show_forgot'] = ($prefs['forgotPass'] == 'y' && $prefs['change_password'] == 'y')? 'y': 'n';
	$module_params['show_register'] = ($prefs['allowRegister'] === 'y')? 'y': 'n';
	$smarty->assign('module_params', $module_params);
	if ($error == PASSWORD_INCORRECT && ($prefs['unsuccessful_logins'] >= 0 || $prefs['unsuccessful_logins_invalid'] >= 0)) {
		$nb_bad_logins = $userlib->unsuccessful_logins($requestedUser);
		$nb_bad_logins++ ; 
		$userlib->set_unsuccessful_logins($requestedUser, $nb_bad_logins);
		if ($prefs['unsuccessful_logins_invalid'] > 0 && ($nb_bad_logins >= $prefs['unsuccessful_logins_invalid'])) {
			$info = $userlib->get_user_info($requestedUser);
			$userlib->change_user_waiting($requestedUser, 'a');
			$msg = sprintf(tra('%d or more unsuccessful login attempts have been made.'), $prefs['unsuccessful_logins_invalid']);
			$msg .= ' '.tra('Your account has been suspended.').' '.tra('Contact your site administrator to reactivate it.');
			$smarty->assign('msg', $msg);
			if ($nb_bad_logins % $prefs['unsuccessful_logins_invalid'] == 0) {
				//don't send an email after every failed login
			        include_once ('lib/webmail/tikimaillib.php');
			        $mail = new TikiMail();
			        $smarty->assign('mail_user', $requestedUser);
			        $foo = parse_url($_SERVER['REQUEST_URI']);
			        $mail_machine = $tikilib->httpPrefix(true).str_replace('tiki-login.php', '', $foo['path']);
			        $smarty->assign('mail_machine', $mail_machine);
			        $mail->setText($smarty->fetch('mail/unsuccessful_logins_suspend.tpl'));
			        $mail->setSubject($smarty->fetch('mail/unsuccessful_logins_suspend_subject.tpl'));
			        $emails = !empty($prefs['validator_emails'])?preg_split('/,/', $prefs['validator_emails']): (!empty($prefs['sender_email'])? array($prefs['sender_email']): '');
			        if (!$mail->send(array($info['email'])) || !$mail->send($emails)) {
				        $smarty->assign('msg', tra("The mail can't be sent. Contact the administrator"));
				        $smarty->display("error.tpl");
				        die;
			        }
			}
			$smarty->assign('mid', 'tiki-information.tpl');
			$smarty->display('tiki.tpl');
			die;
		} elseif ($prefs['unsuccessful_logins'] > 0 && ($nb_bad_logins >= $prefs['unsuccessful_logins'])) {
			$msg = sprintf(tra('%d or more unsuccessful login attempts have been made.'), $prefs['unsuccessful_logins']);
			$smarty->assign('msg', $msg);
			if ($nb_bad_logins % $prefs['unsuccessful_logins'] == 0) {
				//don't send an email after every failed login
			        if ($userlib->send_confirm_email($requestedUser, 'unsuccessful_logins')) {
				        $smarty->assign('msg', $msg . ' ' . tra('An email has been sent to you with the instructions to follow.'));
			        }
			}
			$show_history_back_link = 'y';
			$smarty->assign_by_ref('show_history_back_link', $show_history_back_link);
			$smarty->assign('mid', 'tiki-information.tpl');
			$smarty->display("tiki.tpl");
			die;
		}
	}
	switch ($error) {
		case PASSWORD_INCORRECT:
			$error = tra('Invalid username or password');
        		break;

		case USER_NOT_FOUND:
			$smarty->assign('error_login', $error);
			$smarty->assign('mid', 'tiki-login.tpl');
			$smarty->assign('error_user', $_REQUEST["user"]);
			$smarty->display('tiki.tpl');
			exit;

		case ACCOUNT_DISABLED:
			$error = tra('Account requires administrator approval.');
        		break;

		case ACCOUNT_WAITING_USER:
			$error = tra('You did not validate your account.');
			$extraButton = array('href'=>'tiki-send_mail.php?user='. urlencode($_REQUEST['user']), 'text'=>tra('Resend'), 'comment'=>tra('You should have received an email. Check your mailbox and your spam box. Otherwise click on the button to resend the email'));
        		break;
 
		case USER_AMBIGOUS:
			$error = tra('You must use the right case for your username.');
        		break;

		case USER_NOT_VALIDATED:
			$error = tra('You are not yet validated.');
        		break;

		case USER_ALREADY_LOGGED:
			$error = tra('You are already logged in.');
        		break;

		case EMAIL_AMBIGUOUS:
			$error = tra("There is more than one user account with this email. Please contact the administrator.");
			break;

		default:
			$error = tra('Invalid username or password');
	}
	if (isset($extraButton)) $smarty->assign_by_ref('extraButton', $extraButton);

	//	Report error "inline" with the login module
	$smarty->assign('error_login', $error);
	$smarty->assign('mid', 'tiki-login.tpl');
	$smarty->display('tiki.tpl');
	exit;
}

if ( isset($user) ) {
	TikiLib::events()->trigger('tiki.user.login',
		array(
			'type' => 'user',
			'object' => $user,
			'user' => $user,
		)
	);
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

// Check if a wizard should be run.
// If a wizard is run, it will return to the $url location when it has completed. Thus no code after $wizardlib->onLogin will be executed
// The user must be actually logged in before onLogin is called. If $isdue is set, then: "Note that the user is not logged in he's just validated to change his password"
if (!$isdue) {

	if ($prefs['feature_user_encryption'] === 'y') {
		// Notify CryptLib about the login
		$cryptlib = TikiLib::lib('crypt');
		$cryptlib->onUserLogin($pass);
	}

	// Process wizard
	$wizardlib = TikiLib::lib('wizard');
	$wizardlib->onLogin($user, $url);
}

header('Location: ' . $url);
exit;
