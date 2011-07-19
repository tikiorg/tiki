<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');

if ((isset($prefs['email_due']) && $prefs['email_due'] < 0 ) && $prefs['validateUsers'] != 'y') {
	$smarty->assign('msg', tra('This feature is disabled') . ': validateUsers');
	$smarty->display('error.tpl');
	die;
}

if (isset($_REQUEST['user']) && isset($_REQUEST['pass'])) {
	if ($userlib->confirm_email($_REQUEST['user'], $_REQUEST['pass'])) {
		if (empty($user)) {
			$_SESSION["$user_cookie_site"] = $user = $_REQUEST['user'];
		}
		header('Location: tiki-information.php?msg='.urlencode(tra('Account validated successfully.')));
		die;
	}
}

$smarty->assign('msg', tra('Problem. Try to log in again to receive new confirmation instructions.'));
$smarty->display('error.tpl');
