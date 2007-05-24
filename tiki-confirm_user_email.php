<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-confirm_user_email.php,v 1.1 2007-05-24 14:30:47 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

if (!isset($email_due) || $email_due < 0) {
	$smarty->assign('msg', tra('This feature is disabled'));
	$smarty->display('error.tpl');
	die;
}

if (isset($_REQUEST['user']) && isset($_REQUEST['pass'])) {
	if ($userlib->confirm_email($_REQUEST['user'], $_REQUEST['pass'])) {
		$smarty->assign('msg', tra('Account validated successfully.'));
		$_SESSION["$user_cookie_site"] = $user = $_REQUEST['user'];
		$smarty->assign('user', $user);
		$smarty->display("information.tpl");
		die;
	}
}
$smarty->assign('msg', tra('Problem. Try to log in again to receive new confirmation instructions.'));
$smarty->display('error.tpl');
?>
