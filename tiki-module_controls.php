<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-module_controls.php,v 1.4 2003-08-07 20:56:09 zaufi Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/usermodules/usermoduleslib.php');

global $smarty;

$check_req = (isset($_REQUEST["unassign"])
	|| isset($_REQUEST["up"]) || isset($_REQUEST["down"]) || isset($_REQUEST["left"]) || isset($_REQUEST["right"]));

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
	if (isset($_REQUEST["up"]))
		$usermoduleslib->swap_up_user_module($_REQUEST["up"], $user);
	elseif (isset($_REQUEST["down"]))
		$usermoduleslib->swap_down_user_module($_REQUEST["down"], $user);
	elseif (isset($_REQUEST["left"]))
		$usermoduleslib->set_column_user_module($_REQUEST["left"], $user, 'l');
	elseif (isset($_REQUEST["right"]))
		$usermoduleslib->set_column_user_module($_REQUEST["right"], $user, 'r');
	else
		$usermoduleslib->unassign_user_module($_REQUEST["unassign"], $user);
}

// TODO: Need to fix this stupid way... Must replace only my own args... (or not?)
$pos = strpos($_SERVER["REQUEST_URI"], "?");

if ($pos)
	$url = substr($_SERVER["REQUEST_URI"], 0, $pos);
else
	$url = $_SERVER["REQUEST_URI"];

$smarty->assign('current_location', $url);

?>