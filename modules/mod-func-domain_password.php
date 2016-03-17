<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
		'description' => tra('Store personal passwords for other domains securely in Tiki'),
		'prefs' => array('feature_user_encryption'),
		'params' => array(
			'domain' => array(
				'name' => tra('Domain'),
				'description' => tra('System the credentials apply for. The name must match a defined Password Domain'),
			),
			'use_currentuser' => array(
				'name' => tra('Use current user'),
				'description' => tra('Use the currently logged-in user. The username is not editable. (y/n) Default: y'),
			),
			'can_update' => array(
				'name' => tra('Can Update'),
				'description' => tra('If "y" the user can update the values, otherwise the display is read-only (y/n). Default: n'),
			),
			'show_domain_prompt' => array(
				'name' => tra('Show domain prompt'),
				'description' => tra('If "y" the word "domain" is shown before the domain. Otherwise the domain name takes the full row (y/n). Default: y'),
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
	global $prefs, $user;
	$smarty = TikiLib::lib('smarty');
	$tikilib = TikiLib::lib('tiki');

	// Allow for multiple modules on one page
	$moduleNr = $mod_reference['moduleId'];
	$moduleNr = str_replace('wikiplugin_', '', $moduleNr); // Remove the leading wikiplugin_ when used in a wiki page
	$cntModule = intval($moduleNr);
	$dompwdCount = 0;
	if (isset($_REQUEST['dompwdCount'])) {
		$dompwdCount = intval($_REQUEST['dompwdCount']);
	}
	$smarty->assign('dompwdCount', $cntModule);


	// Use a static array of smarty variables, to support multiple modules on a single page
	static $errors = array();
	$errors[$cntModule] = array();

	static $can_update = array();
	static $edit_option = array();
	static $use_currentuser = array();
	static $username = array();
	static $domainDisplayPrompt = array();

	$hasDomain = false;

	// Determine domain
	$domain = '';
	if (!empty($module_params['domain'])) {
		$domain = $module_params['domain'];
		$smarty->assign('domain', $domain);
	}

	// Domain display option
	$domainDisplayPrompt[$cntModule] = 'y';
	if (!empty($module_params['show_domain_prompt'])) {
		$domainDisplayPrompt[$cntModule] = $module_params['show_domain_prompt'];
	}
	$smarty->assign('domainDisplayPrompt', $domainDisplayPrompt);


	if (empty($user)) {
		$errors[$cntModule][] = tra('You are not logged in');
	} else {

		try {
			$cryptlib = TikiLib::lib('crypt');
			$cryptlib->init();

			// Determine domain
			if (!empty($domain)) {
				// Validate the domain
				$allDomains = $cryptlib->getPasswordDomains();
				if (!$allDomains) {
					$errors[$cntModule][] = tra('No Password Domains found');
				} elseif (!in_array($domain, $allDomains)) {
					$errors[$cntModule][] = tra('Domain is not valid');
				} else {
					$hasDomain = true;
				}
			} else {
				$errors[$cntModule][] = tra('No domain specified');
			}

			// Determine if writable
			$can_update[$cntModule] = 'n';
			if (!empty($module_params['can_update'])) {
				$can_update[$cntModule] = $module_params['can_update'];
			}

			$isSaving = isset($_REQUEST['saveButton'.$cntModule]) ? true : false;

			// Determine user
			$use_currentuser[$cntModule] = 'y';
			if (!empty($module_params['use_currentuser'])) {
				$use_currentuser[$cntModule] = $module_params['use_currentuser'];
			}
			if ($use_currentuser[$cntModule] == 'y') {
				$username[$cntModule] = $user;
				$smarty->assign('currentuser', $use_currentuser);
				$smarty->assign('username', $username);
			} else {
				$smarty->assign('currentuser', $use_currentuser);
				$username[$cntModule] = $cryptlib->getUserData($domain, 'usr');
				if (!empty($username[$cntModule])) {
					$smarty->assign('username', $username);
				} else {
					if ($isSaving == false) {
						$errors[$cntModule][] = tra('No user defined');
					}
				}
			}

			// Check if editing
			$edit_option[$cntModule] = 'n';
			if ($can_update[$cntModule] == 'y' && (!isset($_REQUEST['edit_form'.$cntModule]) || $_REQUEST['edit_form'.$cntModule] != 'y'))
			{
				// Only enable editing, after the user clicks the edit link
				$can_update[$cntModule] = 'n';
				$edit_option[$cntModule] = 'y';
			}
			$smarty->assign('edit_option', $edit_option);
			$smarty->assign('can_update', $can_update);

			// Check stored data if they can be decrypted
			if (!empty($username[$cntModule]) && $isSaving == false) {
				$chkPwd = $cryptlib->hasUserData($domain);
				if ($chkPwd == false) {
					if ($isSaving == false) {
						$errors[$cntModule][] = tra('No password saved');
					}
				} else {
					$chkPwd = $cryptlib->getUserData($domain);
					if ($chkPwd == false) {
						$errors[$cntModule][] = tra('Read error');
					}
				}
			}

			// Saved the credentials
			/////////////////////////////////
			if (($dompwdCount == $cntModule) && $isSaving && $hasDomain && isset($_REQUEST['domPassword'])) {
				if(empty($_REQUEST['domPassword'])) {
					$errors[$cntModule][] = tra('No password specified');
				} elseif(!$use_currentuser[$cntModule] && empty($_REQUEST['domUsername'])) {
					$errors[$cntModule][] = tra('No username specified');
				} else {
					$domUsername = $use_currentuser[$cntModule] === 'y' ? $user : $_REQUEST['domUsername'];
					$domPassword = $_REQUEST['domPassword'];

					if (!$cryptlib->setUserData($domain, $domPassword)) {
						$errors[$cntModule][] = tra('Failed to save password');
					} else {
						if (!$cryptlib->setUserData($domain, $domUsername, 'usr')) {
							$errors[$cntModule][] = tra('Failed to save user');
						} else {
							// Refresh the displayed username is saved ok
							$username[$cntModule] = $domUsername;
							$smarty->assign('username', $username);

							// Format result
							$result = array();
							$result[$cntModule] = tra('Saved OK');
							$smarty->assign('result', $result);
						}
					}
				}
			}
		} catch(Exception $e) {
			$errors[$cntModule][] = $e->getMessage();
		}
	}
	if (!empty($errors[$cntModule])) {
		$smarty->assign('errors', $errors);
	}
}
