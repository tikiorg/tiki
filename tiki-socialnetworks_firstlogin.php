<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$inputConfiguration = array(
	array( 'staticKeyFilters' =>
		array(
			'userlogin' => 'striptags',
			'userpass' => 'striptags',
		)
	)
);

require_once ('tiki-setup.php');
$access->check_user($user);

$smarty->assign('msg', '');
$smarty->assign('alldone', false);

$smarty->assign('userinfo', $userlib->get_user_info($user));

//groups choice
if (count($registrationlib->merged_prefs['choosable_groups'])) {
    $smarty->assign('listgroups', $registrationlib->merged_prefs['choosable_groups']);
    if (count($registrationlib->merged_prefs['choosable_groups']) == 1) {
        $smarty->assign_by_ref('theChoiceGroup', $registrationlib->merged_prefs['choosable_groups'][0]['groupName']);
    }
}

if (isset($_REQUEST["localinfosubmit"])) {
	if ($prefs['login_is_email'] === 'y' && empty($_REQUEST["name"])) {
		$smarty->assign('msg', tra('Email is mandatory'));
	} else if (empty($_REQUEST["name"]) || (empty($_REQUEST["email"]) && $prefs['login_is_email'] !== 'y')) {
		$smarty->assign('msg', tra('Username and email are mandatory'));
	} elseif ($user !== $_REQUEST['name'] && $userlib->user_exists($_REQUEST['name'])) {
		$smarty->assign('msg', tra('User already exists'));
	} elseif (!preg_match('/^[_a-z0-9\.\-]+@[_a-z0-9\.\-]+\.[a-z]{2,4}$/i',
				($prefs['login_is_email'] !== 'y') ? $_REQUEST['email'] : $_REQUEST['name'])) {
		$smarty->assign('msg', tra('The email address is invalid'));
	} else {
		$tikilib->set_user_preference($user, 'socialnetworks_user_firstlogin', 'n');
		if ($prefs['user_unique_email'] != 'y' || !$userlib->other_user_has_email($user, $_REQUEST['email'])) {
			$userlib->change_user_email($user, $_REQUEST["email"]);
		}
		$userlib->change_login($user, $_REQUEST["name"]);
		$user = $_REQUEST["name"];
		$_SESSION[$user_cookie_site] = $user;
		if (isset($_REQUEST['chosenGroup']) && $userlib->get_registrationChoice($_REQUEST['chosenGroup']) == 'y') {
			$userlib->set_default_group($user, $_REQUEST['chosenGroup']);
		}
		$smarty->assign('alldone', true);
	}
}

if (isset($_REQUEST["linkaccount"])) {
	list($isvalid, $newuser, $error) = $userlib->validate_user($_REQUEST["userlogin"], $_REQUEST["userpass"]);
	if (!$isvalid) {
		$smarty->assign('msg', tra('Invalid username or password'));
	} else {
		$facebook_id = $tikilib->get_user_preference($user, 'facebook_id');
		// TODO set other social networking IDs
		$tikilib->set_user_preference($newuser, 'socialnetworks_user_firstlogin', 'n');
		$tikilib->set_user_preference($newuser, 'facebook_id', $facebook_id);
		$tikilib->set_user_preference($user, 'facebook_id', '');
		$userlib->remove_user($user);
		$user = $newuser;
		$_SESSION[$user_cookie_site] = $user;
		$smarty->assign('alldone', true);
	}
}

TikiLib::lib('registration')->addRegistrationFormValidationJs();

$smarty->assign('mid_data', $smarty->fetch('tiki-socialnetworks_firstlogin.tpl'));
$smarty->display('tiki_full.tpl');
