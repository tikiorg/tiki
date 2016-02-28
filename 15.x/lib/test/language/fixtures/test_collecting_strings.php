<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * simple_set_toggle 
 * 
 * @param mixed $feature 
 * @access public
 * @return void
 */
function simple_set_toggle($feature)
{
	global $prefs;
	$tikilib = TikiLib::lib('tiki');
	$logslib = TikiLib::lib('logs');
	if (isset($_REQUEST[$feature]) && $_REQUEST[$feature] == 'on') {
		if ((!isset($prefs[$feature]) || $prefs[$feature] != 'y')) {
			// not yet set at all or not set to y
			if ($tikilib->set_preference($feature, 'y')) {
				add_feedback($feature, tr('%0 enabled', $feature), 1, 1);
				$logslib->add_action('feature', $feature, 'system', 'enabled');
			}
		}
	} else {
		if ((!isset($prefs[$feature]) || $prefs[$feature] != 'n')) {
			// not yet set at all or not set to n
			if ($tikilib->set_preference($feature, 'n')) {
				add_feedback($feature, tr('%0 disabled', $feature), 0, 1);
				$logslib->add_action('feature', $feature, 'system', 'disabled');
			}
		}
	}

	$cachelib = TikiLib::lib('cache');
	$cachelib->invalidate('allperms');
}

if (isset($_REQUEST['page'])) {
	$adminPage = $_REQUEST['page'];
	if ($adminPage == 'features') {
		$admintitle = 'Features'; //get_strings tra('Features')
		$description = 'Enable/disable Tiki features here, but configure them elsewhere'; //get_strings tra('Enable/disable Tiki features here, but configure them elsewhere')
		$helpUrl = 'Features+Admin';
		include_once ('tiki-admin_include_features.php');
	} else if ($adminPage == 'general') {
		$admintitle = 'General'; //get_strings tra('General')
		$description = 'General preferences and settings'; //get_strings tra('General preferences and settings')
		$helpUrl = 'General+Admin';
		include_once ('tiki-admin_include_general.php');
	} else if ($adminPage == 'login') {
		$admintitle = 'Login'; //get_strings tra('Login')
		$description = 'User registration, login and authentication'; //get_strings tra('User registration, login and authentication')
		$helpUrl = 'Login+Config';
		include_once ('tiki-admin_include_login.php');
	} else if ($adminPage == 'wiki') {
		$admintitle = tra('Wiki');
		$description = tra('Wiki settings');
		$helpUrl = 'Wiki+Config';
		include_once ('tiki-admin_include_wiki.php');
	} else {
		$helpUrl = '';
	}
	$url = 'tiki-admin.php' . '?page=' . $adminPage;
	if (!$helpUrl) {
		$helpUrl = ucfirst($adminPage) . '+Config';
	}
	$helpDescription = "Help on $admintitle Config"; //get_strings tra("Help on $admintitle Config")

} else {
	$smarty->assign('admintitle', 'Control Panels');
	$smarty->assign('description', 'Home Page for Administrators');
	$smarty->assign('headtitle', breadcrumb_buildHeadTitle($crumbs));
	$smarty->assign('description', $crumbs[0]->description);
	$email_test_body = tra("Congratulations!\n\nYour server can send emails.\n\n");
}
