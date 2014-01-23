<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/**
 * @return array
 */
function module_domain_password_info()
{
	return array(
		'name' => tra('Domain Password'),
		'description' => tra('Store personal passwords for other domains securely in Tiki.'),
		'prefs' => array('feature_user_encryption'),
		'params' => array(
			'domain' => array(
				'name' => tra('Domain'),
				'description' => tra('System the credentials apply for. The name must match a defined Password Domain.'),
			),
			'use_currentuser' => array(
				'name' => tra('Use current user'),
				'description' => tra('Use the currently logged in user. The username is not editable. (y/n) Default: y'),
			),
			'can_update' => array(
				'name' => tra('Can Update'),
				'description' => tra('If "y" the user can update the values, otherwise the display is read-only (y/n). Default: n'),
			),
		),
		'common_params' => array('nonums', 'rows')
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_domain_password($mod_reference, $module_params)
{
	global $prefs, $tikilib, $smarty, $user;
	$cryptlib = TikiLib::lib('crypt');
	$cryptlib->init();

	$hasDomain = false;
	$errors = array();

	if (!isset($user)) {
		$errors[] = "You are not logged in";
	}

	if (!empty($module_params['domain'])) {
		$domain = $module_params['domain'];
		$smarty->assign('domain', $domain);

		// Validate the domain
		$allDomains = $cryptlib->getPasswordDomains();
		if (!$allDomains) {
			$errors[] = 'No Password Domains found';
		} elseif (!in_array($domain, $allDomains)) {
			$errors[] = 'Domain is not defined';
		} else {
			$hasDomain = true;
		}
	} else {
		$errors[] = 'No domain specified';
	}
	$can_update = 'n';
	if (!empty($module_params['can_update'])) {
		$can_update = $module_params['can_update'];
	}

	$use_currentuser = 'y';
	if (!empty($module_params['use_currentuser'])) {
		$use_currentuser = $module_params['use_currentuser'];
	}
	if ($use_currentuser == 'y') {
		$smarty->assign('currentuser', 'y');
		$smarty->assign('username', $user);
	} else {
		$smarty->assign('currentuser', 'n');
		$username = $cryptlib->getUserData($user, $domain, 'user');
		if (!empty($username)) {
			$smarty->assign('username', $username);
		}
	}

	// Check if editing
	$edit_option = 'n';
	if ($can_update == 'y' && (!isset($_REQUEST['edit_form']) || $_REQUEST['edit_form'] != 'y'))
	{
		// Only enable editing, after the user clicks the edit link
		$can_update = 'n';
		$edit_option= 'y';
	}
	$smarty->assign('edit_option', $edit_option);
	$smarty->assign('can_update', $can_update);

	$isSaving = isset($_REQUEST['saveButton']) ? true : false;

	// Saved the credentials
	/////////////////////////////////
	if ($isSaving && $hasDomain && isset($_REQUEST['domPassword'])) {
		if(empty($_REQUEST['domPassword'])) {
			$errors[] = 'No password specified';
		} elseif(!$use_currentuser && empty($_REQUEST['domUsername'])) {
			$errors[] = 'No username specified';
		} else {
			$username = $use_currentuser === 'y' ? $user : $_REQUEST['domUsername'];
			$password = $_REQUEST['domPassword'];

			if (!$cryptlib->setUserData($user, $domain, $password)) {
				$errors[] = 'Failed to save password';
			} else {
				if (!$cryptlib->setUserData($user, $domain, $username, 'user')) {
					$errors[] = 'Failed to save user';
				} else {
					$smarty->assign('result', 'Saved OK');
				}
			}
		}
	}

	if (!empty($errors)) {
		$smarty->assign('errors', $errors);
	}
}
