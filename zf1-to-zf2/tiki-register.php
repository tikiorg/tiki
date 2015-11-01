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
	array('staticKeyFilters' => array(
		'email' => 'email',
		'name' => 'text',
		'pass' => 'text',
		'passAgain' => 'text',
	))
);

$auto_query_args = array();

require_once ('tiki-setup.php');

ask_ticket('register');

if (isset($redirect) && !empty($redirect)) {
	header('Location: '.$redirect);
	exit;
}

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

if ($prefs['allowRegister'] != 'y') {
	header("location: index.php");
	die;
}

if (!empty($prefs['registerKey']) && (empty($_GET['key']) || $_GET['key'] !== $prefs['registerKey'])) {
	$access->redirect('', '', 404);
}

global $user, $prefs;
if (!empty($prefs['feature_alternate_registration_page']) && $prefs['feature_alternate_registration_page'] !== 'tiki-register.php') {
	header("location: " . $prefs['feature_alternate_registration_page']);
	die;
}
$smarty->assign('user_exists', TikiLib::lib('user')->user_exists($user));

$re = $userlib->get_group_info(isset($_REQUEST['chosenGroup']) ? $_REQUEST['chosenGroup'] : 'Registered');
$tr = TikiLib::lib('trk')->get_tracker($re['usersTrackerId']);
if (!empty($tr['description'])) {
	$smarty->assign('userTrackerHasDescription', true);
}

$smarty->assign('mid', 'tiki-register.tpl');
$smarty->display('tiki.tpl');
