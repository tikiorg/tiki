<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-module_controls.php,v 1.7 2003-08-14 14:14:22 zaufi Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/usermodules/usermoduleslib.php');
//include_once ('lib/debug/debugger.php');

global $smarty;

$check_req = (isset($_REQUEST["mc_unassign"])
           || isset($_REQUEST["mc_up"])
           || isset($_REQUEST["mc_down"])
           || isset($_REQUEST["mc_move"]));

if ($tiki_p_configure_modules != 'y' && $check_req) {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));
	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if ($user_assigned_modules != 'y' && $check_req) {
	$smarty->assign('msg', tra("This feature is disabled"));
	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if (!$user && $check_req) {
	$smarty->assign('msg', tra("You must log in to use this feature"));
	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if ($check_req) {
//    global $debugger;
//    $debugger->msg('Module control clicked: '.$check_req);
    // Make defaults if user still ot configure modules for himself
    if (!$usermoduleslib->user_has_assigned_modules($user))
        $usermoduleslib->create_user_assigned_modules($user);
    // Handle control icon click
	if (isset($_REQUEST["mc_up"]))
		$usermoduleslib->swap_up_user_module($_REQUEST["mc_up"], $user);
	elseif (isset($_REQUEST["mc_down"]))
		$usermoduleslib->swap_down_user_module($_REQUEST["mc_down"], $user);
	elseif (isset($_REQUEST["mc_move"]))
		$usermoduleslib->move_module($_REQUEST["mc_move"], $user);
	else
		$usermoduleslib->unassign_user_module($_REQUEST["mc_unassign"], $user);
}

// TODO: Need to fix this stupid way... Must replace only my own args... (or not?)
$pos = strpos($_SERVER["REQUEST_URI"], "?");

if ($pos)
	$url = substr($_SERVER["REQUEST_URI"], 0, $pos);
else
	$url = $_SERVER["REQUEST_URI"];

$smarty->assign('current_location', $url);

?>