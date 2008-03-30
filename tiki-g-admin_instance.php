<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-g-admin_instance.php,v 1.14 2007-10-12 07:55:27 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

include_once ('lib/Galaxia/ProcessManager.php');
include_once ('lib/Galaxia/API.php');

if ($prefs['feature_workflow'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_workflow");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_admin_workflow != 'y') {
	$smarty->assign('msg', tra("Permission denied"));

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST['iid'])) {
	$smarty->assign('msg', tra("No instance indicated"));

	$smarty->display("error.tpl");
	die;
}

/*if (!isset($_REQUEST['aid'])) {
	$smarty->assign('msg', tra("No activity indicated"));

	$smarty->display("error.tpl");
	d*/

$smarty->assign('iid', $_REQUEST['iid']);
//$smarty->assign('aid', $_REQUEST['aid']);

// Get workitems and list the workitems with an option to edit workitems for
// this instance
if (isset($_REQUEST['save'])) {
	check_ticket('g-admin-instance');
	//status, owner
	$instanceManager->set_instance_status($_REQUEST['iid'], $_REQUEST['status']);
	$instanceManager->set_instance_name($_REQUEST['iid'],$_REQUEST['name']);
	$instanceManager->set_instance_owner($_REQUEST['iid'], $_REQUEST['owner']);

	if ($_REQUEST['sendto']) {
		$instanceManager->set_instance_destination($_REQUEST['iid'], $_REQUEST['sendto']);
	}
//process sendto
}

// Get the instance and set instance information
$ins_info = $instanceManager->get_instance($_REQUEST['iid']);
$smarty->assign_by_ref('ins_info', $ins_info);

// Get the process from the instance and set information
$proc_info = $processManager->get_process($ins_info['pId']);
$smarty->assign_by_ref('proc_info', $proc_info);

// Process activities
$activities = $activityManager->list_activities($ins_info['pId'], 0, -1, 'flowNum_asc', '', '');
$smarty->assign('activities', $activities['data']);

// Users
$users = $userlib->get_users(0, -1, 'login_asc', '');
$smarty->assign_by_ref('users', $users['data']);

$props = $instanceManager->get_instance_properties($_REQUEST['iid']);

if (isset($_REQUEST['unsetprop'])) {
	check_ticket('g-admin-instance');
	unset ($props[$_REQUEST['unsetprop']]);

	$instanceManager->set_instance_properties($_REQUEST['iid'], $props);
}

if (!is_array($props))
	$props = array();

$smarty->assign_by_ref('props', $props);

if (isset($_REQUEST['addprop'])) {
	check_ticket('g-admin-instance');
	$props[$_REQUEST['name']] = $_REQUEST['value'];

	$instanceManager->set_instance_properties($_REQUEST['iid'], $props);
}

if (isset($_REQUEST['saveprops'])) {
	check_ticket('g-admin-instance');
	foreach (array_keys($_REQUEST['props'])as $key) {
		$props[$key] = $_REQUEST['props'][$key];
	}

	$instanceManager->set_instance_properties($_REQUEST['iid'], $props);
}
$acts = $instanceManager->get_instance_activities($_REQUEST['iid']);
$smarty->assign_by_ref('acts', $acts);

$instance->getInstance($_REQUEST['iid']);

// Process comments
if (isset($_REQUEST['__removecomment'])) {
	check_ticket('g-admin-instance');
	$__comment = $instance->get_instance_comment($_REQUEST['__removecomment']);

	if ($__comment['user'] == $user or $tiki_p_admin_workflow == 'y') {
		$instance->remove_instance_comment($_REQUEST['__removecomment']);
	}
}

$smarty->assign_by_ref('__comments', $__comments);

if (!isset($_REQUEST['__cid']))
	$_REQUEST['__cid'] = 0;

if (isset($_REQUEST['__post'])) {
	check_ticket('g-admin-instance');
	$instance->replace_instance_comment($_REQUEST['__cid'], $_REQUEST['aid'], '', $user, $_REQUEST['__title'], $_REQUEST['__comment']);
}

//$__comments = $instance->get_instance_comments($_REQUEST['aid']);
$smarty->assign('comments',$__comments);

ask_ticket('g-admin-instance');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->assign('mid', 'tiki-g-admin_instance.tpl');
$smarty->display("tiki.tpl");

?>
