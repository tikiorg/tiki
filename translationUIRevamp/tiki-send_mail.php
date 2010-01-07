<?php
// $Id$
require_once('tiki-setup.php');
if ($prefs['validateUsers'] != 'y') {
	$smarty->assign('msg', tra('This feature is disabled') . ': validateUsers');
	$smarty->display('error.tpl');
	die;
}
if (!empty($_REQUEST['user'])) {
	$user_info = $userlib->get_user_info($_REQUEST['user']);
	$userlib->send_validation_email($user_info['login'], $user_info['valid'], $user_info['email']);
	$smarty->assign('msg', tra('An email has been sent to you with the instructions to follow.'));
} else {
	$smarty->assign('msg', tra("The mail can't be sent. Contact the administrator"));
}
$smarty->assign('mid', 'tiki-information.tpl');
$smarty->display('tiki.tpl');
	