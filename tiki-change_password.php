<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-change_password.php,v 1.20.2.2 2008/03/20 17:03:31 jyhem Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

if ($prefs['change_password'] != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("Permission denied"));
	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["user"]))
	$_REQUEST["user"] = '';

if (!isset($_REQUEST["oldpass"]))
	$_REQUEST["oldpass"] = '';

$smarty->assign('userlogin', $_REQUEST["user"]);
$smarty->assign('oldpass', $_REQUEST["oldpass"]);

if (isset($_REQUEST["change"])) {
	check_ticket('change-password');
	// Check that pass and pass2 match, otherwise display error and exit
	if ($_REQUEST["pass"] != $_REQUEST["pass2"]) {
		$smarty->assign('msg', tra("The passwords do not match"));
		$smarty->display("error.tpl");
		die;
	}

	// Check that new password is different from old password, otherwise display error and exit
	if ($_REQUEST["pass"] == $_REQUEST["oldpass"]) {
		$smarty->assign('msg', tra("You can not use the same password again"));
		$smarty->display("error.tpl");
		die;
	}

	$polerr = $userlib->check_password_policy($_REQUEST["pass"]);
	if ( strlen($polerr)>0 ) {
		$smarty->assign('msg',$polerr);
	    $smarty->display("error.tpl");
	    die;
	}

	if (empty($_REQUEST['oldpass']) && !empty($_REQUEST['actpass'])) {
		$_REQUEST['oldpass'] = $userlib->activate_password($_REQUEST['user'], $_REQUEST['actpass']);
		if (empty($_REQUEST['oldpass'])) {
			$smarty->assign('msg', tra('Invalid username or activation code. Maybe this code has already been used.'));
			$smarty->display('error.tpl');
			die;
		}
	}
	// Check that provided user name could log in with old password, otherwise display error and exit
	list($isvalid, $_REQUEST["user"], $error) = $userlib->validate_user($_REQUEST["user"], $_REQUEST["oldpass"], '', '');
	if (!$isvalid) {
		$smarty->assign('msg', tra("Invalid old password or unknown user"));
		$smarty->display("error.tpl");
		die;
	}

	$userlib->change_user_password($_REQUEST["user"], $_REQUEST["pass"]);
	// Login the user and display Home page
	$_SESSION["$user_cookie_site"] = $_REQUEST["user"];
	header ('Location: '.$prefs['tikiIndex']);
}
ask_ticket('change-password');

// Display the template
global $prefs;
$prefs['language'] = $tikilib->get_user_preference($_REQUEST["user"], "language", $prefs['site_language']);

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->assign('mid', 'tiki-change_password.tpl');
$smarty->display("tiki.tpl");

?>
