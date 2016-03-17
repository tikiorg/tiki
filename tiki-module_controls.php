<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
$usermoduleslib = TikiLib::lib('usermodules');
$smarty = TikiLib::lib('smarty');
global $tiki_p_configure_modules, $prefs, $user;
$check_req = (isset($_REQUEST["mc_unassign"]) || isset($_REQUEST["mc_up"]) || isset($_REQUEST["mc_down"]) || isset($_REQUEST["mc_move"]));
if ($tiki_p_configure_modules != 'y' && $check_req) {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}
if ($prefs['user_assigned_modules'] != 'y' && $check_req) {
	$smarty->assign('msg', tra("This feature is disabled") . ": user_assigned_modules");
	$smarty->display("error.tpl");
	die;
}
if (!$user && $check_req) {
	$smarty->assign('msg', tra("You must log in to use this feature"));
	$smarty->display("error.tpl");
	die;
}
$request_uri = $url = isset($_SERVER["REQUEST_URI"]) ? $_SERVER['REQUEST_URI'] : '';
if ($check_req) {
	//    global $debugger;
	//    $debugger->msg('Module control clicked: '.$check_req);
	// Make defaults if user still ot configure modules for himself
	if (!$usermoduleslib->user_has_assigned_modules($user)) $usermoduleslib->create_user_assigned_modules($user);
	// Handle control icon click
	if (isset($_REQUEST["mc_up"])) $usermoduleslib->swap_up_user_module($_REQUEST["mc_up"], $user);
	elseif (isset($_REQUEST["mc_down"])) $usermoduleslib->swap_down_user_module($_REQUEST["mc_down"], $user);
	elseif (isset($_REQUEST["mc_move"])) $usermoduleslib->move_module($_REQUEST["mc_move"], $user);
	else $usermoduleslib->unassign_user_module($_REQUEST["mc_unassign"], $user);
	// Remove module movemet paramaters from an URL
	// \todo What if 'mc_xxx' arg was not at the end? (if smbd fix URL by hands...)
	//       should I handle this very special (hack?) case?
	$url = preg_replace('/(.*)(\?|&){1}(mc_up|mc_down|mc_move|mc_unassign)=[^&]*/', '\1', $url);
}
// Fix locaton if parameter was removed...
if ($url != $request_uri) {
	$access = TikiLib::lib('access');
	$access->redirect($url);
}
$smarty->assign('current_location', $url);
$smarty->assign('mpchar', (strpos($url, '?') ? '&' : '?'));
