<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// As a side note beyond the standard heading. Most of the code in this file was taken
// directly from the OpenID library example files. The code was modified to suit the
// specific needs.
require_once ('tiki-setup.php');
/**
 * Require the OpenID consumer code.
 */
require_once "Auth/OpenID/Consumer.php";
/**
 * Require the "file store" module, which we'll need to store
 * OpenID information.
 */
require_once "Auth/OpenID/FileStore.php";
/**
 * Require the Simple Registration extension API.
 */
require_once "Auth/OpenID/SReg.php";
if ($prefs['auth_method'] != 'openid') {
	$smarty->assign('msg', tra("Authentication method is not OpenID"));
	$smarty->display("error.tpl");
	die;
}
function setupFromAddress() // {{{
{
	global $url_scheme, $url_host, $url_port, $base_url;
	// Remember where the page was requested from (from tiki-login.php)
	if (!isset($_SESSION['loginfrom'])) {
		$_SESSION['loginfrom'] = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $prefs['tikiIndex']);
		if (!preg_match('/^http/', $_SESSION['loginfrom'])) {
			if ($_SESSION['loginfrom'] {
				0
			} == '/') $_SESSION['loginfrom'] = $url_scheme . '://' . $url_host . (($url_port != '') ? ":$url_port" : '') . $_SESSION['loginfrom'];
			else $_SESSION['loginfrom'] = $base_url . $_SESSION['loginfrom'];
		}
	}
	if (strpos($_SESSION['loginfrom'], 'openid') !== false) $_SESSION['loginfrom'] = $base_url;
} // }}}
function getAccountsMatchingIdentifier($identifier) // {{{
{
	global $tikilib;
	$result = $tikilib->query("SELECT login FROM users_users WHERE openid_url = ?", array($identifier));
	$userlist = array();
	while ($row = $result->fetchRow()) $userlist[] = $row['login'];
	return $userlist;
} // }}}
function loginUser($identifier) // {{{
{
	global $user_cookie_site, $userlib;
	$userlib->update_lastlogin($identifier);
	$userlib->update_expired_groups();
	$_SESSION[$user_cookie_site] = $identifier;
	header('location: ' . $_SESSION['loginfrom']);
	unset($_SESSION['loginfrom']);
	exit;
} // }}}
function filterExistingInformation(&$data, &$messages) // {{{
{
	global $tikilib;
	$result = $tikilib->query("SELECT COUNT(*) FROM users_users WHERE login = ?", array($data['nickname']));
	$count = reset($result->fetchRow());
	if ($count > 0) {
		$data['nickname'] = '';
		$messages[] = tra('Your default nickname is already in use. A new one has to be selected.');
	}
} // }}}
function displayRegisatrationForms($data, $messages) // {{{
{
	global $smarty, $userlib, $prefs;
	global $registrationlib; require_once('lib/registration/registrationlib.php');

	if (is_a($registrationlib->merged_prefs, "RegistrationError")) {
		register_error($registrationlib->merged_prefs->msg);
	}
	$smarty->assign_by_ref('merged_prefs', $registrationlib->merged_prefs);
	
	
	// Default values for the registration form
	$smarty->assign('username', $data['nickname']);
	$smarty->assign('email', $data['email']);
	// Changing some system values to get the login box to display properly in the context
	$smarty->assign('rememberme', 'disabled');
	$smarty->assign('forgotPass', 'n');
	$smarty->assign('allowRegister', ($prefs['allowRegister'] != 'y' || ($prefs['feature_intertiki'] == 'y' && !empty($prefs['feature_intertiki_mymaster']))) ? 'n' : 'y');
	$smarty->assign('change_password', 'n');
	$smarty->assign('auth_method', 'tiki');
	$smarty->assign('feature_switch_ssl_mode', 'n');

	$listgroups = $userlib->get_groups(0, -1, 'groupName_asc', '', '', 'n');
	$nbChoiceGroups = 0;
	$mandatoryChoiceGroups = true;
	foreach($listgroups['data'] as $gr) {
		if ($gr['registrationChoice'] == 'y') {
			++$nbChoiceGroups;
			$theChoiceGroup = $gr['groupName'];
			if ($gr['groupName'] == 'Registered') $mandatoryChoiceGroups = false;
		}
	}
	if ($nbChoiceGroups) {
		$smarty->assign('listgroups', $listgroups['data']);
		if ($nbChoiceGroups == 1) {
			$smarty->assign_by_ref('theChoiceGroup', $theChoiceGroup);
		}
	}

	// Display
	$smarty->assign('mid', 'tiki-register.tpl');
	$smarty->assign('openid_associate', 'y');
	$smarty->assign('registration', 'y');
	$smarty->display('tiki.tpl');
	exit;
} // }}}
function displaySelectionList($data, $messages) // {{{
{
	global $smarty;
	// Display
	$smarty->assign('mid', 'tiki-openid_select.tpl');
	$smarty->display('tiki.tpl');
	exit;
} // }}}
function displayError($message) { // {{{
	global $smarty;
	$smarty->assign('msg', tra("Failure:") . " " . $message);
	$smarty->assign('errortype', 'login');
	$smarty->display("error.tpl");
	die;
} // }}}
function getStore() { // {{{
	$store_path = "temp/openid_consumer";
	if (!file_exists($store_path) && !mkdir($store_path)) {
		print "Could not create the FileStore directory '$store_path'. " . " Please check the effective permissions.";
		exit(0);
	}
	return new Auth_OpenID_FileStore($store_path);
} // }}}
function getConsumer() { // {{{
	
	/**
	 * Create a consumer object using the store object created
	 * earlier.
	 */
	$store = getStore();
	return new Auth_OpenID_Consumer($store);
} // }}}
function getOpenIDURL() { // {{{
	// Render a default page if we got a submission without an openid
	// value.
	if (empty($_GET['openid_url'])) {
		displayError('Call the page properly');
	}
	return $_GET['openid_url'];
} // }}}
function getScheme() { // {{{
	$scheme = 'http';
	if (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') {
		$scheme.= 's';
	}
	return $scheme;
} // }}}
function getReturnTo() { // {{{
	$path = str_replace('\\','/',dirname($_SERVER['PHP_SELF']));
	$string = sprintf("%s://%s:%s%s/tiki-login_openid.php?action=return", getScheme(), $_SERVER['SERVER_NAME'], $_SERVER['SERVER_PORT'], $path == '/' ? '' : $path);
	if (isset($_GET['action']) && $_GET['action'] == 'force') $string.= '&force=true';
	return $string;
} // }}}
function getTrustRoot() { // {{{
	return sprintf("%s://%s:%s%s", getScheme(), $_SERVER['SERVER_NAME'], $_SERVER['SERVER_PORT'], str_replace('\\','/',dirname($_SERVER['PHP_SELF'])));
} // }}}
function runAuth() { // {{{
	setupFromAddress();
	$openid = getOpenIDURL();
	$consumer = getConsumer();
	// Begin the OpenID authentication process.
	$auth_request = $consumer->begin($openid);
	// No auth request means we can't begin OpenID. Usually this is because the OpenID is invalid. Sometimes this is because the OpenID server's certificate isn't trusted.
	if (!$auth_request) {
		displayError(tra("Authentication error; probably not a valid OpenID."));
	}
	$sreg_request = Auth_OpenID_SRegRequest::build(
	// Required
	array(),
	// Optional
	array('nickname', 'email'));
	if ($sreg_request) {
		$auth_request->addExtension($sreg_request);
	}
	// Redirect the user to the OpenID server for authentication.
	// Store the token for this authentication so we can verify the
	// response.
	// For OpenID 1, send a redirect.  For OpenID 2, use a Javascript
	// form to send a POST request to the server.
	if ($auth_request->shouldSendRedirect()) {
		$redirect_url = $auth_request->redirectURL(getTrustRoot(), getReturnTo());
		// If the redirect URL can't be built, display an error
		// message.
		if (Auth_OpenID::isFailure($redirect_url)) {
			displayError(tra("Could not redirect to server: ") . $redirect_url->message);
		} else {
			// Send redirect.
			header("Location: " . $redirect_url);
		}
	} else {
		// Generate form markup and render it.
		$form_id = 'openid_message';
		$form_html = $auth_request->htmlMarkup(getTrustRoot(), getReturnTo(), false, array('id' => $form_id));
		// Display an error if the form markup couldn't be generated;
		// otherwise, render the HTML.
		if (Auth_OpenID::isFailure($form_html)) {
			displayError(tra("Could not redirect to server: ") . $form_html->message);
		} else {
			print $form_html;
		}
	}
} // }}}
function runFinish() { // {{{
	global $smarty;
	$consumer = getConsumer();
	// Complete the authentication process using the server's
	// response.
	$response = $consumer->complete(getReturnTo());
	// Check the response status.
	if ($response->status == Auth_OpenID_CANCEL) {
		// This means the authentication was cancelled.
		displayError(tra('Verification cancelled.'));
	} else if ($response->status == Auth_OpenID_FAILURE) {
		// Authentication failed; display the error message.
		displayError(tra("OpenID authentication failed: ") . $response->message);
	} else if ($response->status == Auth_OpenID_SUCCESS) {
		// This means the authentication succeeded; extract the
		// identity URL and Simple Registration data (if it was
		// returned).
		$data = array('identifier' => $response->identity_url, 'email' => '', 'fullname' => '', 'nickname' => '',);
		$sreg_resp = Auth_OpenID_SRegResponse::fromSuccessResponse($response);
		$sreg = $sreg_resp->contents();
		// Sanitize identifier. Just consider slashes at the end are never good.
		if (substr($data['identifier'], -1) == '/') $data['identifier'] = substr($data['identifier'], 0, -1);
		if (@$sreg['email']) $data['email'] = $sreg['email'];
		if (@$sreg['nickname']) $data['nickname'] = $sreg['nickname'];
		$_SESSION['openid_url'] = $data['identifier'];
		// If OpenID identifier exists in the database
		$list = getAccountsMatchingIdentifier($data['identifier']);
		$_SESSION['openid_userlist'] = $list;
		$smarty->assign('openid_userlist', $list);
		if (count($list) > 0 && !isset($_GET['force'])) {
			// If Single account
			if (count($list) == 1) {
				// Login the user
				loginUser($list[0]);
			} else
			// Else Multiple account
			{
				// Display user selection list
				displaySelectionList($list);
			}
		} else {
			$messages = array();
			// Check for entries that already exist in the database and filter them out
			filterExistingInformation($data, $messages);
			// Display register and attach forms
			displayRegisatrationForms($data, $messages);
		}
	}
} // }}}
function runSelect() // {{{
{
	setupFromAddress();
	$user = $_GET['select'];
	if (in_array($user, $_SESSION['openid_userlist'])) loginUser($user);
	else displayError(tra('The selected account is not associated with your identity.'));
} // }}}
if (isset($_GET['action'])) {
	if ($_GET['action'] == 'return') runFinish();
	elseif ($_GET['action'] == 'select' && isset($_GET['select'])) runSelect();
	elseif ($_GET['action'] == 'force') runAuth();
	else displayError(tra('unknown action'));
} else runAuth();
