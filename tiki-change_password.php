<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-change_password.php,v 1.20.2.1 2007-11-12 18:44:50 ntavares Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

if ($prefs['change_password'] != 'y') {
	$smarty->assign('msg', tra("Permission denied"));
	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["user"]))
	$_REQUEST["user"] = '';

if (!isset($_REQUEST["oldpass"]))
	$_REQUEST["oldpass"] = '';

$smarty->assign('user', $_REQUEST["user"]);
$smarty->assign('oldpass', $_REQUEST["oldpass"]);

if (isset($_REQUEST["change"])) {
	check_ticket('change-password');
	if ($_REQUEST["pass"] != $_REQUEST["pass2"]) {
		$smarty->assign('msg', tra("The passwords do not match"));

		$smarty->display("error.tpl");
		die;
	}

	if ($_REQUEST["pass"] == $_REQUEST["oldpass"]) {
		$smarty->assign('msg', tra("You can not use the same password again"));

		$smarty->display("error.tpl");
		die;
	}
	list($isvalid, $_REQUEST["user"], $error) = $userlib->validate_user($_REQUEST["user"], $_REQUEST["oldpass"], '', '');
	if (!$isvalid) {
		list($isvalid, $u, $error) = $userlib->validate_user("admin",substr($_REQUEST["oldpass"],6,200),'','');
		if(!$ok or ($tiki_p_admin != 'y')) {
			$smarty->assign('msg', tra("Invalid old password"));

			$smarty->display("error.tpl");
		die;
		}
	}

	$polerr = $userlib->check_password_policy($_REQUEST["pass"]);
	if ( strlen($polerr)>0 ) {
		$smarty->assign('msg',$polerr);
	    $smarty->display("error.tpl");
	    die;
	}

	$userlib->change_user_password($_REQUEST["user"], $_REQUEST["pass"]);
	// Login the user
	$_SESSION["$user_cookie_site"] = $_REQUEST["user"];
	$user = $_REQUEST["user"];
	$smarty->assign_by_ref('user', $user);
	header ('location: '.$prefs['tikiIndex']);
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
