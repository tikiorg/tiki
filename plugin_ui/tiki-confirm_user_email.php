<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-confirm_user_email.php,v 1.3 2007-10-12 07:55:25 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

if (!isset($prefs['email_due']) || $prefs['email_due'] < 0) {
	$smarty->assign('msg', tra('This feature is disabled'));
	$smarty->display('error.tpl');
	die;
}

if (isset($_REQUEST['user']) && isset($_REQUEST['pass'])) {
	if ($userlib->confirm_email($_REQUEST['user'], $_REQUEST['pass'])) {
		$_SESSION["$user_cookie_site"] = $user = $_REQUEST['user'];
		header('Location: tiki-information.php?msg='.urlencode('Account validated successfully.'));
		die;
	}
}

$smarty->assign('msg', tra('Problem. Try to log in again to receive new confirmation instructions.'));
$smarty->display('error.tpl');
?>
