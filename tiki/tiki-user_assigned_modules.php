<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-user_assigned_modules.php,v 1.12 2004-03-28 07:32:23 mose Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/usermodules/usermoduleslib.php');

if ($tiki_p_configure_modules != 'y') {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if ($user_assigned_modules != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": user_assigned_modules");

	$smarty->display("error.tpl");
	die;
}

if (!$user) {
	$smarty->assign('msg', tra("You must log in to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST["recreate"])) {
	check_ticket('user-modules');
	$usermoduleslib->create_user_assigned_modules($user);
}

if (!$usermoduleslib->user_has_assigned_modules($user)) {
//	check_ticket('user-modules');
	$usermoduleslib->create_user_assigned_modules($user);
}

if (isset($_REQUEST["unassign"])) {
	check_ticket('user-modules');
	$usermoduleslib->unassign_user_module($_REQUEST["unassign"], $user);
}

if (isset($_REQUEST["assign"])) {
	check_ticket('user-modules');
	$usermoduleslib->assign_user_module($_REQUEST["module"], $_REQUEST["position"], $_REQUEST["order"], $user);
}

if (isset($_REQUEST["up"])) {
	check_ticket('user-modules');
	$usermoduleslib->up_user_module($_REQUEST["up"], $user);
}

if (isset($_REQUEST["down"])) {
	check_ticket('user-modules');
	$usermoduleslib->down_user_module($_REQUEST["down"], $user);
}

if (isset($_REQUEST["left"])) {
	check_ticket('user-modules');
	$usermoduleslib->set_column_user_module($_REQUEST["left"], $user, 'l');
}

if (isset($_REQUEST["right"])) {
	check_ticket('user-modules');
	$usermoduleslib->set_column_user_module($_REQUEST["right"], $user, 'r');
}

$orders = array();

for ($i = 1; $i < 20; $i++) {
	$orders[] = $i;
}

$smarty->assign_by_ref('orders', $orders);

$assignables = $usermoduleslib->get_user_assignable_modules($user);

if (count($assignables) > 0) {
	$smarty->assign('canassign', 'y');
} else {
	$smarty->assign('canassign', 'n');
}

//print_r($assignables);
$modules = $usermoduleslib->get_user_assigned_modules($user);

$smarty->assign('modules_l', $usermoduleslib->get_user_assigned_modules_pos($user, 'l'));
$smarty->assign('modules_r', $usermoduleslib->get_user_assigned_modules_pos($user, 'r'));

//print_r($modules);
$smarty->assign_by_ref('assignables', $assignables);
$smarty->assign_by_ref('modules', $modules);
//print_r($modules);
include_once ('tiki-mytiki_shared.php');

ask_ticket('user-modules');

$smarty->assign('mid', 'tiki-user_assigned_modules.tpl');
$smarty->display("tiki.tpl");

?>
