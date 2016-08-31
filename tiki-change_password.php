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
		'passAgain' => 'none',
		'oldpass' => 'none',
	) )
);
require_once ('tiki-setup.php');

$access->check_feature('change_password');

if (empty($_REQUEST['user']) || !$userlib->user_exists($_REQUEST['user'])) {
	$smarty->assign('msg', tra('Invalid username'));
	$smarty->assign('errortype', 'login');
	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["oldpass"]))
	$_REQUEST["oldpass"] = '';

if (isset($_REQUEST["newuser"]) && $_REQUEST["newuser"] == 'y') {
	$smarty->assign('new_user_validation', 'y');
}

$smarty->assign('userlogin', $_REQUEST["user"]);
$smarty->assign('oldpass', $_REQUEST["oldpass"]);

if (isset($_REQUEST["change"])) {
	check_ticket('change-password');
	// Check that pass and passAgain match, otherwise display error and exit
	if ($_REQUEST["pass"] != $_REQUEST["passAgain"]) {
		$smarty->assign('msg', tra("The passwords do not match"));
		$smarty->assign('errortype', 'no_redirect_login');
		$smarty->display("error.tpl");
		die;
	}

	// Check that new password is different from old password, otherwise display error and exit
	if ($_REQUEST["pass"] == $_REQUEST["oldpass"]) {
		$smarty->assign('msg', tra("You can not use the same password again"));
		$smarty->assign('errortype', 'no_redirect_login');
		$smarty->display("error.tpl");
		die;
	}

	$polerr = $userlib->check_password_policy($_REQUEST["pass"]);
	if ( strlen($polerr)>0 ) {
		$smarty->assign('msg', $polerr);
		$smarty->assign('errortype', 'no_redirect_login');
	    $smarty->display("error.tpl");
	    die;
	}

	if (empty($_REQUEST['oldpass']) && !empty($_REQUEST['actpass'])) {
		$_REQUEST['oldpass'] = $userlib->activate_password($_REQUEST['user'], $_REQUEST['actpass']);
		if (empty($_REQUEST['oldpass'])) {
			$smarty->assign('msg', tra('Invalid username or activation code. Maybe this code has already been used.'));
			$smarty->assign('errortype', 'no_redirect_login');
			$smarty->display('error.tpl');
			die;
		}
	}
	// Check that provided user name could log in with old password, otherwise display error and exit
	list($isvalid, $_REQUEST["user"], $error) = $userlib->validate_user($_REQUEST["user"], $_REQUEST["oldpass"], '', '');
	if (!$isvalid) {
		$smarty->assign('msg', tra("Invalid old password"));
		$smarty->assign('errortype', 'no_redirect_login');
		$smarty->display("error.tpl");
		die;
	}
	if (isset($_REQUEST['email'])) {
		if (empty($_REQUEST['email']) || !validate_email($_REQUEST['email'], $prefs['validateEmail'])) {
			$smarty->assign('msg', tra('Your email could not be validated; make sure you email is correct'));
			$smarty->assign('errortype', 'no_redirect_login');
			$smarty->display("error.tpl");
			die;			
		}
		$userlib->change_user_email_only($_REQUEST['user'], $_REQUEST['email']);
	}

	$userlib->change_user_password($_REQUEST["user"], $_REQUEST["pass"]);
	// Login the user and display Home page
	$_SESSION["$user_cookie_site"] = $_REQUEST["user"];
	$logslib->add_log('login', 'logged from change_password', $_REQUEST['user'], '', '', $tikilib->now);

	if ($prefs['feature_user_encryption'] === 'y') {
		// Notify CryptLib about the password change
		$cryptlib = TikiLib::lib('crypt');
		$cryptlib->onChangeUserPassword($_REQUEST["oldpass"], $_REQUEST["pass"]);
	}

	$homePageUrl = $prefs['tikiIndex'];	// set up in lib/setup/default_homepage.php

	// Check if a wizard should be run.
	// If a wizard is run, it will return to the $url location when it has completed. Thus no code after $wizardlib->onLogin will be executed
	$wizardlib = TikiLib::lib('wizard');
	$force = $_REQUEST["user"] == 'admin';
	$wizardlib->onLogin($user, $homePageUrl, $force);

	// Go to homepage or url_after_validation
	$accesslib = TikiLib::lib('access');
	if (!empty($prefs['url_after_validation']) && !empty($_REQUEST['new_user_validation'])) {
		$access->redirect($prefs['url_after_validation']);
	} else {
		$accesslib->redirect($homePageUrl);
	}
}
ask_ticket('change-password');

// Display the template
global $prefs;
$prefs['language'] = $tikilib->get_user_preference($_REQUEST['user'], 'language', $prefs['site_language']);
$smarty->assign('email', $userlib->get_user_email($_REQUEST['user']));

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->assign('mid', 'tiki-change_password.tpl');
$smarty->display("tiki.tpl");
